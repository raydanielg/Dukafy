import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/api/api_client.dart';
import 'login_screen.dart';
import 'widgets/auth_background.dart';

class ApprovalScreen extends ConsumerStatefulWidget {
  const ApprovalScreen({super.key, required this.name, required this.phone});

  static const routeName = 'approval';
  static const routePath = '/approval';

  final String name;
  final String phone;

  @override
  ConsumerState<ApprovalScreen> createState() => _ApprovalScreenState();
}

class _ApprovalScreenState extends ConsumerState<ApprovalScreen>
    with SingleTickerProviderStateMixin {
  bool _loading = false;
  bool _approved = false;
  String? _selectedRole; // 'owner' or 'cashier'
  Map<String, dynamic>? _selectedManager;
  final _managerSearchController = TextEditingController();
  List<dynamic> _managerSuggestions = [];
  bool _searchingManager = false;

  late final AnimationController _controller;
  late final Animation<double> _scale;
  late final Animation<double> _fade;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 750),
    );

    _scale = CurvedAnimation(parent: _controller, curve: Curves.elasticOut);
    _fade = CurvedAnimation(parent: _controller, curve: Curves.easeOut);

    _controller.forward();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _searchManagers(String query) async {
    if (query.length < 2) {
      setState(() => _managerSuggestions = []);
      return;
    }

    setState(() => _searchingManager = true);
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/managers/search', queryParameters: {'q': query});
      setState(() => _managerSuggestions = res.data as List);
    } catch (_) {
      // ignore
    } finally {
      setState(() => _searchingManager = false);
    }
  }

  Future<void> _approve() async {
    if (_loading) return;
    if (_selectedRole == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select your role first')),
      );
      return;
    }
    if (_selectedRole == 'cashier' && _selectedManager == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select your manager')),
      );
      return;
    }

    setState(() => _loading = true);

    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.post(
        '/auth/approve',
        data: {
          'role': _selectedRole,
          'manager_id': _selectedManager?['id'],
        },
      );
      final data = res.data;
      final user = (data is Map) ? data['user'] : null;
      final approved = (user is Map) ? user['is_approved'] : null;

      setState(() {
        _approved = approved == true;
      });

      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Approved. Welcome!')),
      );

      // TODO: navigate to dashboard when dashboard route is added.
      context.go(LoginScreen.routePath);
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.toString())),
      );
    } finally {
      if (!mounted) return;
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    final statusText = _approved ? 'Approved' : 'Pending approval';
    final statusColor = _approved ? Colors.green : Colors.orange;

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
                  FadeTransition(
                    opacity: _fade,
                    child: ScaleTransition(
                      scale: _scale,
                  if (!_approved) ...[
                    const Text(
                      'Choose your role',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          child: _RoleCard(
                            title: 'Business Owner',
                            icon: Icons.store_rounded,
                            selected: _selectedRole == 'owner',
                            onTap: () => setState(() {
                              _selectedRole = 'owner';
                              _selectedManager = null;
                            }),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _RoleCard(
                            title: 'Employee / Cashier',
                            icon: Icons.person_pin_rounded,
                            selected: _selectedRole == 'cashier',
                            onTap: () => setState(() => _selectedRole = 'cashier'),
                          ),
                        ),
                      ],
                    ),
                    if (_selectedRole == 'cashier') ...[
                      const SizedBox(height: 20),
                      const Text(
                        'Find your manager',
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                      ),
                      const SizedBox(height: 10),
                      TextFormField(
                        controller: _managerSearchController,
                        onChanged: _searchManagers,
                        decoration: InputDecoration(
                          hintText: 'Type manager name...',
                          prefixIcon: const Icon(Icons.search),
                          suffixIcon: _searchingManager
                              ? const Padding(
                                  padding: EdgeInsets.all(12),
                                  child: SizedBox(
                                    width: 16,
                                    height: 16,
                                    child: CircularProgressIndicator(strokeWidth: 2),
                                  ),
                                )
                              : null,
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.black.withValues(alpha: 0.1)),
                          ),
                        ),
                      ),
                      if (_managerSuggestions.isNotEmpty && _selectedManager == null)
                        Container(
                          margin: const EdgeInsets.only(top: 4),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withValues(alpha: 0.05),
                                blurRadius: 10,
                              ),
                            ],
                          ),
                          child: ListView.builder(
                            shrinkWrap: true,
                            padding: EdgeInsets.zero,
                            itemCount: _managerSuggestions.length,
                            itemBuilder: (context, index) {
                              final m = _managerSuggestions[index];
                              return ListTile(
                                title: Text(m['name']),
                                subtitle: Text("${m['business_name']} • ${m['phone']}"),
                                onTap: () => setState(() {
                                  _selectedManager = m;
                                  _managerSearchController.text = m['name'];
                                  _managerSuggestions = [];
                                }),
                              );
                            },
                          ),
                        ),
                      if (_selectedManager != null) ...[
                        const SizedBox(height: 12),
                        Container(
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            color: colorScheme.primary.withValues(alpha: 0.05),
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: colorScheme.primary.withValues(alpha: 0.2)),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  const Icon(Icons.check_circle, color: Colors.green, size: 18),
                                  const SizedBox(width: 8),
                                  const Text('Selected Manager',
                                      style: TextStyle(fontWeight: FontWeight.w800)),
                                  const Spacer(),
                                  TextButton(
                                    onPressed: () => setState(() {
                                      _selectedManager = null;
                                      _managerSearchController.clear();
                                    }),
                                    child: const Text('Change'),
                                  ),
                                ],
                              ),
                              const Divider(),
                              Text("Name: ${_selectedManager!['name']}",
                                  style: const TextStyle(fontWeight: FontWeight.w700)),
                              Text("Phone: ${_selectedManager!['phone']}"),
                              Text("Business: ${_selectedManager!['business_name']}"),
                            ],
                          ),
                        ),
                      ],
                    ],
                    const SizedBox(height: 24),
                  ],
                      child: Container(
                        height: 84,
                        width: 84,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: colorScheme.primary.withValues(alpha: 0.12),
                          border: Border.all(
                            color: colorScheme.primary.withValues(alpha: 0.25),
                            width: 2,
                          ),
                        ),
                        child: Icon(
                          _approved ? Icons.verified_rounded : Icons.hourglass_top_rounded,
                          size: 44,
                          color: colorScheme.primary,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'Thank you',
                    style: TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    'Your account has been created. Confirm to continue.',
                    style: TextStyle(
                      color: Colors.black.withValues(alpha: 0.60),
                      height: 1.4,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 18),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.92),
                      borderRadius: BorderRadius.circular(22),
                      border: Border.all(
                        color: Colors.black.withValues(alpha: 0.06),
                      ),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                              decoration: BoxDecoration(
                                color: statusColor.withValues(alpha: 0.12),
                                borderRadius: BorderRadius.circular(999),
                              ),
                              child: Text(
                                statusText,
                                style: TextStyle(
                                  color: statusColor,
                                  fontWeight: FontWeight.w900,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 14),
                        Text(
                          widget.name,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          widget.phone,
                          style: TextStyle(
                            color: Colors.black.withValues(alpha: 0.65),
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 14),
                        Text(
                          _approved
                              ? 'Your account is approved. You can continue.'
                              : 'Your account is pending. Tap continue to confirm and get approved automatically.',
                          style: TextStyle(
                            color: Colors.black.withValues(alpha: 0.60),
                            height: 1.35,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 18),
                  SizedBox(
                    width: double.infinity,
                    child: FilledButton(
                      onPressed: _approve,
                      style: FilledButton.styleFrom(
                        backgroundColor: colorScheme.primary,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(999),
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
                              style: TextStyle(fontWeight: FontWeight.w900),
                            ),
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
