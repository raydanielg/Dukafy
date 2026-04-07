import 'package:flutter/material.dart';

class ExpensesScreen extends StatelessWidget {
  const ExpensesScreen({super.key});

  static const routeName = 'expenses';
  static const routePath = '/expenses';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Expenses', style: TextStyle(fontWeight: FontWeight.bold))),
      body: const Center(child: Text('Expenses Management Content')),
    );
  }
}
