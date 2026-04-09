import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/storage/storage_providers.dart';
import '../onboarding/onboarding_screen.dart';
import '../auth/login_screen.dart';
import '../dashboard/dashboard_screen.dart';
import '../auth/user_provider.dart';
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
      // 1. Check if biometrics are available and authenticated
      final bio = ref.read(biometricServiceProvider);
      final isBioAvailable = await bio.isBiometricAvailable();
      
      if (isBioAvailable) {
        final authenticated = await bio.authenticate();
        if (!authenticated) {
          // If biometric failed or cancelled, stay or show error
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Authentication failed. Please try again.')),
          );
          // Retry or force login if needed, for now we stay on splash
          return;
        }
      }

      // 2. Go to Dashboard (Persistent Session)
      // Even if offline, we go to dashboard to allow offline data viewing
      try {
        final dio = ref.read(apiClientProvider).dio;
        // Background update user info if online
        dio.get('/auth/me').then((res) {
          if (mounted) {
            ref.read(userProvider.notifier).setUser(res.data['user']);
          }
        }).catchError((_) {});
        
        if (mounted) context.go(DashboardScreen.routePath);
      } catch (e) {
        // If critical error, maybe go to login, but since we have a token, we trust the session
        if (mounted) context.go(DashboardScreen.routePath);
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
