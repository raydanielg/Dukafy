import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import 'login_screen.dart';
import 'widgets/auth_background.dart';

class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({super.key});

  static const routeName = 'forgot_password';
  static const routePath = '/forgot-password';

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final _formKey = GlobalKey<FormState>();
  final _phoneController = TextEditingController();

  InputDecoration _pillDecoration({
    required String hintText,
    required IconData icon,
    Widget? suffix,
    Widget? prefix,
  }) {
    return InputDecoration(
      hintText: hintText,
      prefixIcon: prefix ?? Icon(icon),
      suffixIcon: suffix,
      filled: true,
      fillColor: Colors.grey.withValues(alpha: 0.06),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(999),
        borderSide: BorderSide.none,
      ),
    );
  }

  @override
  void dispose() {
    _phoneController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Scaffold(
      backgroundColor: const Color(0xFFF7F7F7),
      body: Stack(
        fit: StackFit.expand,
        children: [
          const AuthBackground(),
          SafeArea(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(20, 18, 20, 18),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Align(
                    alignment: Alignment.centerRight,
                    child: TextButton(
                      onPressed: () => context.go(LoginScreen.routePath),
                      child: const Text('Back to login'),
                    ),
                  ),
                  const SizedBox(height: 18),
                  const Text(
                    'Forgot password',
                    style: TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    'Enter your phone number and we will send a reset code.',
                    style: TextStyle(
                      color: Colors.black.withValues(alpha: 0.60),
                      height: 1.4,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 18),
                  Form(
                    key: _formKey,
                    child: Column(
                      children: [
                        TextFormField(
                          controller: _phoneController,
                          keyboardType: TextInputType.phone,
                          decoration: _pillDecoration(
                            hintText: 'Enter your phone number',
                            icon: Icons.phone_outlined,
                            prefix: Padding(
                              padding: const EdgeInsets.only(left: 14, right: 10),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Text(
                                    '+255',
                                    style: TextStyle(fontWeight: FontWeight.w800),
                                  ),
                                  const SizedBox(width: 10),
                                  Container(
                                    width: 1,
                                    height: 22,
                                    color: Colors.black.withValues(alpha: 0.12),
                                  ),
                                  const SizedBox(width: 10),
                                  const Icon(Icons.phone_outlined),
                                ],
                              ),
                            ),
                          ),
                          validator: (v) => (v == null || v.trim().length < 9)
                              ? 'Enter a valid phone number'
                              : null,
                        ),
                        const SizedBox(height: 18),
                        Row(
                          children: [
                            Expanded(
                              child: OutlinedButton(
                                onPressed: () => context.go(LoginScreen.routePath),
                                style: OutlinedButton.styleFrom(
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 16),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(999),
                                  ),
                                ),
                                child: const Text('Back'),
                              ),
                            ),
                            const SizedBox(width: 14),
                            Expanded(
                              child: FilledButton(
                                style: FilledButton.styleFrom(
                                  backgroundColor: colorScheme.primary,
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 16),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(999),
                                  ),
                                ),
                                onPressed: () {
                                  if (!_formKey.currentState!.validate()) return;
                                  FocusScope.of(context).unfocus();
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                        'Password reset request sent (demo).',
                                      ),
                                    ),
                                  );
                                  context.go(LoginScreen.routePath);
                                },
                                child: const Text(
                                  'Continue',
                                  style: TextStyle(fontWeight: FontWeight.w900),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
