import 'dart:math' as math;

import 'package:flutter/material.dart';

class AuthBackground extends StatefulWidget {
  const AuthBackground({super.key});

  @override
  State<AuthBackground> createState() => _AuthBackgroundState();
}

class _AuthBackgroundState extends State<AuthBackground>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 8),
    )..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return AnimatedBuilder(
      animation: _controller,
      builder: (context, _) {
        return CustomPaint(
          painter: _AuthBackgroundPainter(
            t: _controller.value,
            primary: colorScheme.primary,
          ),
          child: const SizedBox.expand(),
        );
      },
    );
  }
}

class _AuthBackgroundPainter extends CustomPainter {
  _AuthBackgroundPainter({
    required this.t,
    required this.primary,
  });

  final double t;
  final Color primary;

  @override
  void paint(Canvas canvas, Size size) {
    final bg = Paint()..color = const Color(0xFFF7F7F7);
    canvas.drawRect(Offset.zero & size, bg);

    final dotsPaint = Paint()..color = Colors.black.withOpacity(0.05);
    final spacing = 24.0;
    final dx = (t * spacing * 0.5); // Slower movement
    final dy = (t * spacing * 0.3);

    for (double y = -spacing; y < size.height + spacing; y += spacing) {
      for (double x = -spacing; x < size.width + spacing; x += spacing) {
        final px = (x + dx) % (size.width + spacing) - spacing;
        final py = (y + dy) % (size.height + spacing) - spacing;
        canvas.drawCircle(Offset(px, py), 1.0, dotsPaint);
      }
    }

    // Glow effects for depth without lines
    final glow = Paint()
      ..color = primary.withOpacity(0.07)
      ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 50);

    canvas.drawCircle(Offset(size.width * 0.85, size.height * 0.15), 180, glow);
    canvas.drawCircle(Offset(size.width * 0.15, size.height * 0.75), 200, glow);
  }

  @override
  bool shouldRepaint(covariant _AuthBackgroundPainter oldDelegate) {
    return oldDelegate.t != t || oldDelegate.primary != primary;
  }
}
