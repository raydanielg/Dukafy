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
  late final AnimationController _pulseController;
  late final Animation<double> _pulseOpacity;
  late final Animation<double> _pulseScale;

  @override
  void initState() {
    super.initState();

    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 900),
    );

    _pulseOpacity = Tween<double>(begin: 0.88, end: 1.0).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );
    _pulseScale = Tween<double>(begin: 0.98, end: 1.02).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );

    _pulseController.repeat(reverse: true);
  }

  @override
  void dispose() {
    _pulseController.dispose();
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
    final overlayHeight =
        (MediaQuery.sizeOf(context).height * 0.62).clamp(420.0, 620.0);

    final pages = <_OnboardPageData>[
      const _OnboardPageData(
        title: 'Manage Your Shop Anywhere',
        subtitle: 'Track sales, stock, and expenses in real-time from your phone.',
        icon: Icons.storefront,
        backgroundAsset: 'assets/images/onboarding_1.jpg',
      ),
      const _OnboardPageData(
        title: 'Business Multi-tenancy',
        subtitle: 'Grow your business with multiple branches and staff accounts.',
        icon: Icons.business_center,
        backgroundAsset: 'assets/images/onboarding_2.jpg',
      ),
      const _OnboardPageData(
        title: 'Smarter Decisions',
        subtitle: 'Get automated reports and insights to help you scale faster.',
        icon: Icons.insights,
        backgroundAsset: 'assets/images/onboarding_3.jpg',
      ),
    ];

    final isLast = _index == pages.length - 1;

    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        fit: StackFit.expand,
        children: [
          PageView.builder(
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

              return Stack(
                fit: StackFit.expand,
                children: [
                  AnimatedOpacity(
                    opacity: t,
                    duration: const Duration(milliseconds: 180),
                    child: Image(
                      image: AssetImage(page.backgroundAsset),
                      fit: BoxFit.cover,
                      color: Colors.black.withValues(alpha: 0.12),
                      colorBlendMode: BlendMode.darken,
                    ),
                  ),
                  Positioned(
                    left: 0,
                    right: 0,
                    bottom: 0,
                    child: Container(
                      height: overlayHeight,
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.bottomCenter,
                          end: Alignment.topCenter,
                          colors: [
                            colorScheme.primary.withValues(alpha: 0.85),
                            Colors.black.withValues(alpha: 0.65),
                            Colors.black.withValues(alpha: 0.40),
                            Colors.black.withValues(alpha: 0.18),
                            Colors.transparent,
                          ],
                          stops: const [0.0, 0.28, 0.58, 0.80, 1.0],
                        ),
                      ),
                      child: SafeArea(
                        top: false,
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(22, 22, 22, 16),
                          child: AnimatedOpacity(
                            opacity: t,
                            duration: const Duration(milliseconds: 220),
                            child: Transform.translate(
                              offset: Offset(0, (1 - t) * 10),
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.end,
                                children: [
                                  Container(
                                    width: 74,
                                    height: 74,
                                    decoration: BoxDecoration(
                                      shape: BoxShape.circle,
                                      color: Colors.white,
                                      boxShadow: [
                                        BoxShadow(
                                          color:
                                              Colors.black.withValues(alpha: 0.24),
                                          blurRadius: 20,
                                          offset: const Offset(0, 14),
                                        ),
                                      ],
                                    ),
                                    child: Icon(
                                      page.icon,
                                      size: 34,
                                      color: colorScheme.primary,
                                    ),
                                  ),
                                  const SizedBox(height: 14),
                                  Text(
                                    page.title,
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 28,
                                      height: 1.1,
                                      fontWeight: FontWeight.w900,
                                    ),
                                    textAlign: TextAlign.center,
                                  ),
                                  const SizedBox(height: 10),
                                  Flexible(
                                    child: SingleChildScrollView(
                                      padding: EdgeInsets.zero,
                                      physics: const BouncingScrollPhysics(),
                                      child: Text(
                                        page.subtitle,
                                        style: TextStyle(
                                          color: Colors.white.withValues(alpha: 0.88),
                                          fontSize: 14.5,
                                          height: 1.55,
                                          fontWeight: FontWeight.w600,
                                        ),
                                        textAlign: TextAlign.center,
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 18),
                                  SizedBox(
                                    width: double.infinity,
                                    height: 54,
                                    child: FadeTransition(
                                      opacity:
                                          _saving ? const AlwaysStoppedAnimation(1) : _pulseOpacity,
                                      child: ScaleTransition(
                                        scale: _saving
                                            ? const AlwaysStoppedAnimation(1)
                                            : _pulseScale,
                                        child: FilledButton(
                                          onPressed: _saving
                                              ? null
                                              : () {
                                                  if (isLast) {
                                                    _finish();
                                                    return;
                                                  }

                                                  _controller.nextPage(
                                                    duration: const Duration(
                                                      milliseconds: 260,
                                                    ),
                                                    curve: Curves.easeOut,
                                                  );
                                                },
                                          style: FilledButton.styleFrom(
                                            backgroundColor: colorScheme.primary,
                                            shape: RoundedRectangleBorder(
                                              borderRadius: BorderRadius.circular(16),
                                            ),
                                          ),
                                          child: _saving
                                              ? const SizedBox(
                                                  width: 20,
                                                  height: 20,
                                                  child: CircularProgressIndicator(
                                                    strokeWidth: 2,
                                                    color: Colors.white,
                                                  ),
                                                )
                                              : Text(
                                                  isLast ? 'Get Started' : 'Continue',
                                                  style: const TextStyle(
                                                    fontWeight: FontWeight.w900,
                                                    letterSpacing: 0.2,
                                                  ),
                                                ),
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              );
            },
          ),
        ],
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
