import 'package:flutter/material.dart';

class MembersScreen extends StatelessWidget {
  const MembersScreen({super.key});

  static const routeName = 'members';
  static const routePath = '/members';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Staff & Members', style: TextStyle(fontWeight: FontWeight.bold))),
      body: const Center(child: Text('Members Management Content')),
    );
  }
}
