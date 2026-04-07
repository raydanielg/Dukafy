import 'package:flutter/material.dart';

class LoansScreen extends StatelessWidget {
  const LoansScreen({super.key});

  static const routeName = 'loans';
  static const routePath = '/loans';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Loans & Repayments', style: TextStyle(fontWeight: FontWeight.bold))),
      body: const Center(child: Text('Loans Management Content')),
    );
  }
}
