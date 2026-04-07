import 'package:flutter/material.dart';

class ProductsScreen extends StatelessWidget {
  const ProductsScreen({super.key});

  static const routeName = 'products';
  static const routePath = '/products';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Products Inventory')),
      body: const Center(child: Text('Products Management Content')),
    );
  }
}
