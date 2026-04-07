import 'package:flutter/material.dart';

class SuppliersScreen extends StatelessWidget {
  const SuppliersScreen({super.key});

  static const routeName = 'suppliers';
  static const routePath = '/suppliers';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Suppliers Management', style: TextStyle(fontWeight: FontWeight.bold))),
      body: const Center(child: Text('Suppliers Management Content')),
    );
  }
}
