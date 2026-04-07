import 'package:flutter/material.dart';

class InvoicesScreen extends StatelessWidget {
  const InvoicesScreen({super.key});

  static const routeName = 'invoices';
  static const routePath = '/invoices';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Invoices Management')),
      body: const Center(child: Text('Invoices Management Content')),
    );
  }
}
