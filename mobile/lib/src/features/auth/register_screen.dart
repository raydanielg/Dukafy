import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import 'login_screen.dart';
import 'widgets/auth_background.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  static const routeName = 'register';
  static const routePath = '/register';

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _firstNameController = TextEditingController();
  final _lastNameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  bool _obscurePassword = true;
  bool _obscureConfirm = true;

  @override
  void dispose() {
    _firstNameController.dispose();
    _lastNameController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

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
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Scaffold(
      backgroundColor: const Color(0xFFF7F7F7),
      body: Stack(
        fit: StackFit.expand,
        children: [
          const AuthBackground(),
          SafeArea(
            child: Column(
              children: [
                Expanded(
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.fromLTRB(20, 18, 20, 18),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Create account',
                          style: TextStyle(
                            fontSize: 28,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                        const SizedBox(height: 10),
                        Text(
                          "Fill in your details below to get started. You'll be able to sign in once your account is created.",
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
                                controller: _firstNameController,
                                decoration: _pillDecoration(
                                  hintText: 'First name',
                                  icon: Icons.person_outline,
                                ),
                                validator: (v) =>
                                    (v == null || v.trim().isEmpty) ? 'Enter first name' : null,
                              ),
                              const SizedBox(height: 12),
                              TextFormField(
                                controller: _lastNameController,
                                decoration: _pillDecoration(
                                  hintText: 'Last name',
                                  icon: Icons.person_outline,
                                ),
                                validator: (v) =>
                                    (v == null || v.trim().isEmpty) ? 'Enter last name' : null,
                              ),
                              const SizedBox(height: 12),
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
                                    ? 'Enter valid phone'
                                    : null,
                              ),
                              const SizedBox(height: 12),
                              TextFormField(
                                controller: _passwordController,
                                obscureText: _obscurePassword,
                                decoration: _pillDecoration(
                                  hintText: 'Create a password',
                                  icon: Icons.lock_outline,
                                  suffix: TextButton(
                                    onPressed: () => setState(
                                      () => _obscurePassword = !_obscurePassword,
                                    ),
                                    child: Text(
                                      _obscurePassword ? 'Show' : 'Hide',
                                      style: const TextStyle(fontWeight: FontWeight.w800),
                                    ),
                                  ),
                                ),
                                validator: (v) => (v == null || v.length < 6)
                                    ? 'Password too short'
                                    : null,
                              ),
                              const SizedBox(height: 12),
                              TextFormField(
                                controller: _confirmPasswordController,
                                obscureText: _obscureConfirm,
                                decoration: _pillDecoration(
                                  hintText: 'Confirm your password',
                                  icon: Icons.lock_outline,
                                  suffix: TextButton(
                                    onPressed: () => setState(
                                      () => _obscureConfirm = !_obscureConfirm,
                                    ),
                                    child: Text(
                                      _obscureConfirm ? 'Show' : 'Hide',
                                      style: const TextStyle(fontWeight: FontWeight.w800),
                                    ),
                                  ),
                                ),
                                validator: (v) {
                                  if (v == null || v.isEmpty) {
                                    return 'Confirm password';
                                  }
                                  if (v != _passwordController.text) {
                                    return 'Passwords do not match';
                                  }
                                  return null;
                                },
                              ),
                              const SizedBox(height: 14),
                              Text(
                                'By creating an account, you agree to our Terms of Service and Privacy Policy.',
                                style: TextStyle(
                                  color: Colors.black.withValues(alpha: 0.55),
                                  height: 1.4,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                              const SizedBox(height: 18),
                              Row(
                                children: [
                                  Expanded(
                                    child: OutlinedButton(
                                      onPressed: () => context.go(LoginScreen.routePath),
                                      style: OutlinedButton.styleFrom(
                                        padding: const EdgeInsets.symmetric(vertical: 16),
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
                                      onPressed: () {
                                        if (!_formKey.currentState!.validate()) return;
                                        FocusScope.of(context).unfocus();
                                        // TODO: call API
                                        context.go(LoginScreen.routePath);
                                      },
                                      style: FilledButton.styleFrom(
                                        backgroundColor: colorScheme.primary,
                                        padding: const EdgeInsets.symmetric(vertical: 16),
                                        shape: RoundedRectangleBorder(
                                          borderRadius: BorderRadius.circular(999),
                                        ),
                                      ),
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
          ),
        ],
      ),
    );
  }
}
