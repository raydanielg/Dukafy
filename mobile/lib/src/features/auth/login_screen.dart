import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'forgot_password_screen.dart';
import 'register_screen.dart';
import '../../core/locale/locale_controller.dart';

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
    final locale = ref.watch(localeControllerProvider);
    final isSw = locale.languageCode == 'sw';

    return Scaffold(
      backgroundColor: const Color(0xFFF7F7F7),
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.fromLTRB(20, 18, 20, 18),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        SegmentedButton<String>(
                          segments: const [
                            ButtonSegment(value: 'en', label: Text('EN')),
                            ButtonSegment(value: 'sw', label: Text('SW')),
                          ],
                          selected: {isSw ? 'sw' : 'en'},
                          onSelectionChanged: (value) {
                            final code = value.first;
                            ref
                                .read(localeControllerProvider.notifier)
                                .setLocale(Locale(code));
                          },
                        ),
                        const Spacer(),
                        TextButton(
                          onPressed: () => context.go(RegisterScreen.routePath),
                          child: Text(isSw ? 'Jisajili' : 'Register'),
                        ),
                      ],
                    ),
                    const SizedBox(height: 18),
                    Text(
                      isSw ? 'Ingia' : 'Sign in',
                      style: const TextStyle(
                        fontSize: 28,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                    const SizedBox(height: 10),
                    Text(
                      isSw
                          ? 'Weka taarifa zako kuendelea.'
                          : 'Enter your details below to continue.',
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
                              hintText: isSw
                                  ? 'Weka namba ya simu'
                                  : 'Enter your phone number',
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
                                ? (isSw
                                    ? 'Weka namba sahihi'
                                    : 'Enter a valid phone number')
                                : null,
                          ),
                          const SizedBox(height: 12),
                          TextFormField(
                            controller: _passwordController,
                            obscureText: _obscurePassword,
                            decoration: _pillDecoration(
                              hintText: isSw ? 'Nenosiri' : 'Password',
                              icon: Icons.lock_outline,
                              suffix: TextButton(
                                onPressed: () => setState(
                                  () => _obscurePassword = !_obscurePassword,
                                ),
                                child: Text(
                                  _obscurePassword
                                      ? (isSw ? 'Onesha' : 'Show')
                                      : (isSw ? 'Ficha' : 'Hide'),
                                  style: const TextStyle(fontWeight: FontWeight.w800),
                                ),
                              ),
                            ),
                            validator: (value) {
                              if (value == null || value.isEmpty) {
                                return isSw ? 'Weka nenosiri' : 'Enter your password';
                              }
                              return null;
                            },
                          ),
                          const SizedBox(height: 8),
                          Align(
                            alignment: Alignment.centerRight,
                            child: TextButton(
                              onPressed: () =>
                                  context.go(ForgotPasswordScreen.routePath),
                              child: Text(
                                isSw ? 'Umesahau nenosiri?' : 'Forgot password?',
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 8, 20, 18),
              child: Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => context.go(RegisterScreen.routePath),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(999),
                        ),
                      ),
                      child: Text(isSw ? 'Jisajili' : 'Register'),
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: FilledButton(
                      onPressed: () {
                        if (!_formKey.currentState!.validate()) return;
                        FocusScope.of(context).unfocus();
                        // TODO: call API
                      },
                      style: FilledButton.styleFrom(
                        backgroundColor: colorScheme.primary,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(999),
                        ),
                      ),
                      child: Text(
                        isSw ? 'Endelea' : 'Continue',
                        style: const TextStyle(fontWeight: FontWeight.w900),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
