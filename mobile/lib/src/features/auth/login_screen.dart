import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'auth_repository.dart';
import 'approval_screen.dart';
import 'forgot_password_screen.dart';
import 'register_screen.dart';
import 'widgets/auth_background.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  static const routeName = 'login';
  static const routePath = '/login';

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;
  bool _loading = false;

  @override
  void dispose() {
    _phoneController.dispose();
    _passwordController.dispose();
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
                        Align(
                          alignment: Alignment.centerRight,
                          child: TextButton(
                            onPressed: () =>
                                context.go(RegisterScreen.routePath),
                            child: const Text('Register'),
                          ),
                        ),
                        const SizedBox(height: 18),
                        const Text(
                          'Sign in',
                          style: TextStyle(
                            fontSize: 28,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                        const SizedBox(height: 10),
                        Text(
                          'Enter your details below to continue.',
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
                                    padding:
                                        const EdgeInsets.only(left: 14, right: 10),
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
                              const SizedBox(height: 12),
                              TextFormField(
                                controller: _passwordController,
                                obscureText: _obscurePassword,
                                decoration: _pillDecoration(
                                  hintText: 'Password',
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
                                validator: (value) {
                                  if (value == null || value.isEmpty) {
                                    return 'Enter your password';
                                  }
                                  return null;
                                },
                              ),
                              const SizedBox(height: 8),
                              Align(
                                alignment: Alignment.centerRight,
                                child: TextButton(
                                  onPressed: () => context
                                      .go(ForgotPasswordScreen.routePath),
                                  child: const Text('Forgot password?'),
                                ),
                              ),
                              const SizedBox(height: 14),
                              Row(
                                children: [
                                  Expanded(
                                    child: OutlinedButton(
                                      onPressed: () =>
                                          context.go(RegisterScreen.routePath),
                                      style: OutlinedButton.styleFrom(
                                        padding: const EdgeInsets.symmetric(
                                          vertical: 16,
                                        ),
                                        shape: RoundedRectangleBorder(
                                          borderRadius:
                                              BorderRadius.circular(999),
                                        ),
                                      ),
                                      child: const Text('Register'),
                                    ),
                                  ),
                                  const SizedBox(width: 14),
                                  Expanded(
                                    child: FilledButton(
                                      onPressed: () {
                                        if (_loading) return;
                                        if (!_formKey.currentState!.validate()) return;

                                        FocusScope.of(context).unfocus();

                                        final phone = _phoneController.text.trim();
                                        final password = _passwordController.text;

                                        setState(() => _loading = true);

                                        ref
                                            .read(authRepositoryProvider)
                                            .login(phone: phone, password: password)
                                            .then((result) {
                                          if (!mounted) return;

                                          final isApproved = result.isApproved;
                                          if (!isApproved) {
                                            context.goNamed(
                                              ApprovalScreen.routeName,
                                              extra: {
                                                'name': result.name,
                                                'phone': result.phone,
                                              },
                                            );
                                            return;
                                          }

                                          ScaffoldMessenger.of(context).showSnackBar(
                                            const SnackBar(content: Text('Logged in (demo).')),
                                          );
                                        }).catchError((e) {
                                          if (!mounted) return;
                                          ScaffoldMessenger.of(context).showSnackBar(
                                            SnackBar(
                                              content: Text(e.toString()),
                                            ),
                                          );
                                        }).whenComplete(() {
                                          if (!mounted) return;
                                          setState(() => _loading = false);
                                        });
                                      },
                                      style: FilledButton.styleFrom(
                                        backgroundColor: colorScheme.primary,
                                        padding: const EdgeInsets.symmetric(
                                          vertical: 16,
                                        ),
                                        shape: RoundedRectangleBorder(
                                          borderRadius:
                                              BorderRadius.circular(999),
                                        ),
                                      ),
                                      child: _loading
                                          ? const SizedBox(
                                              height: 18,
                                              width: 18,
                                              child: CircularProgressIndicator(
                                                strokeWidth: 2,
                                                color: Colors.white,
                                              ),
                                            )
                                          : const Text(
                                              'Continue',
                                              style: TextStyle(
                                                fontWeight: FontWeight.w900,
                                              ),
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
