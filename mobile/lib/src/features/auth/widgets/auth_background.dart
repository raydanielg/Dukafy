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

    final dotsPaint = Paint()..color = Colors.black.withValues(alpha: 0.06);
    final spacing = 22.0;
    final dx = (t * spacing * 1.2);
    final dy = (t * spacing * 0.8);

    for (double y = -spacing; y < size.height + spacing; y += spacing) {
      for (double x = -spacing; x < size.width + spacing; x += spacing) {
        final px = (x + dx) % (size.width + spacing) - spacing;
        final py = (y + dy) % (size.height + spacing) - spacing;
        canvas.drawCircle(Offset(px, py), 1.3, dotsPaint);
      }
    }

    final linePaint = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.6
      ..color = primary.withValues(alpha: 0.18);

    final linePaint2 = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.0
      ..color = primary.withValues(alpha: 0.12);

    void drawWave({required double y0, required double amp, required Paint p}) {
      final path = Path();
      for (int i = 0; i <= 80; i++) {
        final x = size.width * (i / 80);
        final phase = (t * 2 * math.pi) + (i / 10);
        final y = y0 + math.sin(phase) * amp;
        if (i == 0) {
          path.moveTo(x, y);
        } else {
          path.lineTo(x, y);
        }
      }
      canvas.drawPath(path, p);
    }

    drawWave(y0: size.height * 0.22, amp: 10, p: linePaint2);
    drawWave(y0: size.height * 0.34, amp: 14, p: linePaint);
    drawWave(y0: size.height * 0.48, amp: 10, p: linePaint2);

    final glow = Paint()
      ..color = primary.withValues(alpha: 0.08)
      ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 40);

    canvas.drawCircle(Offset(size.width * 0.82, size.height * 0.22), 140, glow);
    canvas.drawCircle(Offset(size.width * 0.18, size.height * 0.62), 160, glow);
  }

  @override
  bool shouldRepaint(covariant _AuthBackgroundPainter oldDelegate) {
    return oldDelegate.t != t || oldDelegate.primary != primary;
  }
}
