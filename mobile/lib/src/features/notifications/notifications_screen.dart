import 'package:flutter/material.dart';

class NotificationsScreen extends StatelessWidget {
  const NotificationsScreen({super.key});

  static const routeName = 'notifications';
  static const routePath = '/notifications';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('SMS & Emails', style: TextStyle(fontWeight: FontWeight.bold))),
      body: const Center(child: Text('Notifications Management Content')),
    );
  }
}
