import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/storage/storage_providers.dart';
import '../auth/login_screen.dart';

class OnboardingScreen extends ConsumerStatefulWidget {
  const OnboardingScreen({super.key});

  static const routeName = 'onboarding';
  static const routePath = '/onboarding';

  @override
  ConsumerState<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends ConsumerState<OnboardingScreen> {
  final _controller = PageController();
  int _index = 0;
  bool _saving = false;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _finish() async {
    if (_saving) return;

    setState(() {
      _saving = true;
    });

    await ref.read(secureStorageProvider).setOnboardingDone();

    if (!mounted) return;
    context.go(LoginScreen.routePath);
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    final pages = <_OnboardPageData>[
      const _OnboardPageData(
        title: 'Fast Checkout (POS)',
        subtitle: 'Sell in seconds with a clean POS that reduces errors and saves time.',
        icon: Icons.point_of_sale,
        backgroundAsset: 'assets/images/onboarding_1.jpg',
      ),
      const _OnboardPageData(
        title: 'Inventory & Stock Alerts',
        subtitle: 'Track stock, prices, and get alerts before items run out.',
        icon: Icons.inventory_2,
        backgroundAsset: 'assets/images/onboarding_2.jpg',
      ),
      const _OnboardPageData(
        title: 'Smart Reports',
        subtitle: 'See sales performance, profit trends, and top products anytime.',
        icon: Icons.analytics,
        backgroundAsset: 'assets/images/onboarding_3.jpg',
      ),
    ];

    final isLast = _index == pages.length - 1;

    return Scaffold(
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  TextButton(
                    onPressed: _saving ? null : () => context.go(LoginScreen.routePath),
                    child: const Text('Skip'),
                  ),
                  Row(
                    children: List.generate(pages.length, (i) {
                      final active = i == _index;
                      return AnimatedContainer(
                        duration: const Duration(milliseconds: 220),
                        margin: const EdgeInsets.symmetric(horizontal: 4),
                        height: 8,
                        width: active ? 22 : 8,
                        decoration: BoxDecoration(
                          color: active
                              ? colorScheme.primary
                              : colorScheme.primary.withValues(alpha: 0.22),
                          borderRadius: BorderRadius.circular(99),
                        ),
                      );
                    }),
                  ),
                  const SizedBox(width: 52),
                ],
              ),
            ),
            Expanded(
              child: PageView.builder(
                controller: _controller,
                itemCount: pages.length,
                onPageChanged: (value) {
                  setState(() {
                    _index = value;
                  });
                },
                itemBuilder: (context, index) {
                  final page = pages[index];

                  final pageValue = _controller.hasClients
                      ? (_controller.page ?? _index.toDouble())
                      : _index.toDouble();
                  final delta = (index - pageValue).abs();
                  final t = (1 - delta).clamp(0.0, 1.0);

                  return Padding(
                    padding: EdgeInsets.zero,
                    child: Stack(
                      fit: StackFit.expand,
                      children: [
                        AnimatedOpacity(
                          opacity: t,
                          duration: const Duration(milliseconds: 180),
                          child: Image(
                            image: AssetImage(page.backgroundAsset),
                            fit: BoxFit.cover,
                            color: Colors.black.withValues(alpha: 0.20),
                            colorBlendMode: BlendMode.darken,
                          ),
                        ),
                        DecoratedBox(
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              begin: Alignment.topCenter,
                              end: Alignment.bottomCenter,
                              colors: [
                                Colors.black.withValues(alpha: 0.55),
                                colorScheme.primary.withValues(alpha: 0.18),
                                Colors.black.withValues(alpha: 0.70),
                              ],
                            ),
                          ),
                        ),
                        Align(
                          alignment: Alignment.bottomCenter,
                          child: Padding(
                            padding: const EdgeInsets.fromLTRB(20, 20, 20, 96),
                            child: AnimatedOpacity(
                              opacity: t,
                              duration: const Duration(milliseconds: 220),
                              child: Transform.translate(
                                offset: Offset(0, (1 - t) * 14),
                                child: Column(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Container(
                                      width: 86,
                                      height: 86,
                                      padding: const EdgeInsets.all(14),
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        borderRadius: BorderRadius.circular(26),
                                        boxShadow: [
                                          BoxShadow(
                                            color: Colors.black.withValues(alpha: 0.18),
                                            blurRadius: 22,
                                            offset: const Offset(0, 14),
                                          ),
                                        ],
                                      ),
                                      child: const Image(
                                        image: AssetImage('assets/images/logo.png'),
                                        fit: BoxFit.contain,
                                      ),
                                    ),
                                    const SizedBox(height: 16),
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                        horizontal: 14,
                                        vertical: 8,
                                      ),
                                      decoration: BoxDecoration(
                                        color: Colors.white.withValues(alpha: 0.10),
                                        borderRadius: BorderRadius.circular(999),
                                        border: Border.all(
                                          color: Colors.white.withValues(alpha: 0.18),
                                        ),
                                      ),
                                      child: Row(
                                        mainAxisSize: MainAxisSize.min,
                                        children: [
                                          Icon(
                                            page.icon,
                                            size: 18,
                                            color: Colors.white.withValues(alpha: 0.92),
                                          ),
                                          const SizedBox(width: 8),
                                          Text(
                                            'DUKAFY',
                                            style: TextStyle(
                                              color: Colors.white.withValues(alpha: 0.92),
                                              fontWeight: FontWeight.w800,
                                              letterSpacing: 1.4,
                                              fontSize: 12,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                    const SizedBox(height: 14),
                                    Text(
                                      page.title,
                                      style: const TextStyle(
                                        color: Colors.white,
                                        fontSize: 26,
                                        height: 1.1,
                                        fontWeight: FontWeight.w900,
                                      ),
                                      textAlign: TextAlign.center,
                                    ),
                                    const SizedBox(height: 10),
                                    Text(
                                      page.subtitle,
                                      style: TextStyle(
                                        color: Colors.white.withValues(alpha: 0.86),
                                        fontSize: 14,
                                        height: 1.5,
                                        fontWeight: FontWeight.w600,
                                      ),
                                      textAlign: TextAlign.center,
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(18, 10, 18, 18),
              child: SizedBox(
                width: double.infinity,
                height: 50,
                child: FilledButton(
                  onPressed: _saving
                      ? null
                      : () {
                          if (isLast) {
                            _finish();
                            return;
                          }

                          _controller.nextPage(
                            duration: const Duration(milliseconds: 260),
                            curve: Curves.easeOut,
                          );
                        },
                  style: FilledButton.styleFrom(
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                  ),
                  child: _saving
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : Text(
                          isLast ? 'Get Started' : 'Continue',
                          style: const TextStyle(fontWeight: FontWeight.w800),
                        ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _OnboardPageData {
  const _OnboardPageData({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.backgroundAsset,
  });

  final String title;
  final String subtitle;
  final IconData icon;
  final String backgroundAsset;
}
