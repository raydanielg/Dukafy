import 'package:flutter/material.dart';

class PurchasesScreen extends StatelessWidget {
  const PurchasesScreen({super.key});

  static const routeName = 'purchases';
  static const routePath = '/purchases';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Purchases Management')),
      body: const Center(child: Text('Purchases History Content')),
    );
  }
}
