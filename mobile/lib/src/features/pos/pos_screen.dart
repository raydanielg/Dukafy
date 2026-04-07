import 'package:flutter/material.dart';

class POSScreen extends StatelessWidget {
  const POSScreen({super.key});

  static const routeName = 'pos';
  static const routePath = '/pos';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Point of Sale')),
      body: const Center(child: Text('POS Screen Content')),
    );
  }
}
