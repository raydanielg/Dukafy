import 'package:flutter/material.dart';

class ShopsScreen extends StatelessWidget {
  const ShopsScreen({super.key});

  static const routeName = 'shops';
  static const routePath = '/shops';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('My Shops', style: TextStyle(fontWeight: FontWeight.bold))),
      body: const Center(child: Text('Shops Management Content')),
    );
  }
}
