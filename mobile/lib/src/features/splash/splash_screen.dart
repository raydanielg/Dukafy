import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/storage/storage_providers.dart';
import '../onboarding/onboarding_screen.dart';
import '../auth/login_screen.dart';
import '../dashboard/dashboard_screen.dart';
import '../auth/user_provider.dart';
import '../auth/approval_screen.dart';
import '../../core/api/api_client.dart';
import '../../core/auth/biometric_service.dart';

class SplashScreen extends ConsumerStatefulWidget {
  const SplashScreen({super.key});

  static const routeName = 'splash';
  static const routePath = '/';

  @override
  ConsumerState<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends ConsumerState<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _handleNavigation();
  }

  Future<void> _handleNavigation() async {
    await Future.delayed(const Duration(seconds: 2));
    if (!mounted) return;

    final storage = ref.read(secureStorageProvider);
    final token = await storage.getAuthToken();

    if (token != null && token.isNotEmpty) {
      // NOTE: Biometric authentication temporarily disabled.
      // Uncomment the block below to re-enable fingerprint/face ID on app launch.
      /*
      final bio = ref.read(biometricServiceProvider);
      final isBioAvailable = await bio.isBiometricAvailable();
      
      if (isBioAvailable) {
        final authenticated = await bio.authenticate();
        if (!authenticated) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Authentication failed. Please try again.')),
          );
          return;
        }
      }
      */

      // Check approval status before allowing dashboard access
      try {
        final dio = ref.read(apiClientProvider).dio;
        final res = await dio.get('/auth/me');
        
        if (mounted) {
          final userData = res.data['user'];
          ref.read(userProvider.notifier).setUser(userData);
          
          // Check if user is approved
          final isApproved = userData['is_approved'] == true;
          
          if (!isApproved) {
            // User not approved - must verify first
            context.goNamed(
              ApprovalScreen.routeName,
              extra: {
                'name': userData['name'] ?? '',
                'phone': userData['phone'] ?? '',
              },
            );
            return;
          }
          
          // User is approved - go to Dashboard
          context.go(DashboardScreen.routePath);
        }
      } catch (e) {
        // If API fails (offline or error), check cached user data if available
        // For safety, we'll go to login to re-authenticate
        if (mounted) {
          context.go(LoginScreen.routePath);
        }
      }
    } else {
      final done = await storage.isOnboardingDone();
      context.go(done ? LoginScreen.routePath : OnboardingScreen.routePath);
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Scaffold(
      body: DecoratedBox(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              colorScheme.primary,
              colorScheme.primaryContainer,
            ],
          ),
        ),
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(
                Icons.storefront_rounded,
                size: 80,
                color: Colors.white,
              ),
              const SizedBox(height: 24),
              const Text(
                'Dukafy',
                style: TextStyle(
                  fontSize: 32,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                  letterSpacing: 1.2,
                ),
              ),
              const SizedBox(height: 12),
              const CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
