import 'package:flutter/material.dart';

class BankingScreen extends StatelessWidget {
  const BankingScreen({super.key});

  static const routeName = 'banking';
  static const routePath = '/banking';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Treasury & Banking', style: TextStyle(fontWeight: FontWeight.bold))),
      body: const Center(child: Text('Banking Management Content')),
    );
  }
}
