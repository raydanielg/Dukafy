import 'package:flutter/material.dart';

class CustomersScreen extends StatelessWidget {
  const CustomersScreen({super.key});

  static const routeName = 'customers';
  static const routePath = '/customers';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Customers Management', style: TextStyle(fontWeight: FontWeight.bold))),
      body: const Center(child: Text('Customers Management Content')),
    );
  }
}
