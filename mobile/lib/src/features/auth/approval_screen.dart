import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:lottie/lottie.dart';

import '../../core/api/api_client.dart';
import 'login_screen.dart';
import '../dashboard/dashboard_screen.dart';
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
  final _businessNameController = TextEditingController();
  String? _selectedBusinessType;
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
    _managerSearchController.dispose();
    _businessNameController.dispose();
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
    setState(() => _loading = true);

    try {
      final dio = ref.read(apiClientProvider).dio;
      await dio.post('/auth/approve-initial');
      
      setState(() {
        _approved = true;
      });

      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Account verified! Now choose your role.')),
      );
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

  Future<void> _completeOnboarding() async {
    if (_loading) return;
    if (_selectedRole == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select your role first')),
      );
      return;
    }
    if (_selectedRole == 'owner' && _businessNameController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please enter your business name')),
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
      await dio.post(
        '/auth/complete-onboarding',
        data: {
          'role': _selectedRole,
          'manager_id': _selectedManager?['id'],
          'business_name': _businessNameController.text.trim(),
          'business_type': _selectedBusinessType,
        },
      );
      
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Setup complete. Welcome!')),
      );

      context.go(DashboardScreen.routePath);
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

  Widget _buildDetailRow(String label, String value,
      {Color? valueColor, bool isLast = false}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
      decoration: BoxDecoration(
        border: isLast
            ? null
            : Border(
                bottom: BorderSide(color: Colors.black.withOpacity(0.05))),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              color: Colors.black.withOpacity(0.5),
              fontWeight: FontWeight.w600,
              fontSize: 14,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Text(
              value,
              textAlign: TextAlign.right,
              style: TextStyle(
                color: valueColor ?? Colors.black87,
                fontWeight: FontWeight.w800,
                fontSize: 14,
              ),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final statusText = _approved ? 'Verified' : 'Pending verification';
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
                  const SizedBox(height: 40),
                  Center(
                    child: FadeTransition(
                      opacity: _fade,
                      child: ScaleTransition(
                        scale: _scale,
                        child: Lottie.asset(
                          'assets/icons/lottieflow-success-01-000000-easey.json',
                          height: 150,
                          width: 150,
                          repeat: true,
                          delegates: LottieDelegates(
                            values: [
                              ValueDelegate.color(
                                const ['**'],
                                value: Colors.green,
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ),
                  const Center(
                    child: Text(
                      'Thank you!',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.w900,
                        letterSpacing: -0.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),
                  Center(
                    child: Text(
                      'Your account has been created successfully.\nFollow the steps below to finish setup.',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        color: Colors.black.withOpacity(0.60),
                        height: 1.5,
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  const SizedBox(height: 32),
                  if (!_approved) ...[
                    const Text(
                      'Account Details',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w900,
                        letterSpacing: -0.5,
                      ),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      width: double.infinity,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: Colors.black.withOpacity(0.06)),
                      ),
                      child: Column(
                        children: [
                          _buildDetailRow('Full Name', widget.name),
                          _buildDetailRow('Phone Number', widget.phone),
                          _buildDetailRow('Status', statusText,
                              valueColor: statusColor, isLast: true),
                        ],
                      ),
                    ),
                    const SizedBox(height: 24),
                    SizedBox(
                      width: double.infinity,
                      child: FilledButton(
                        onPressed: _approve,
                        style: FilledButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 18),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12)),
                        ),
                        child: _loading
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child: CircularProgressIndicator(
                                    strokeWidth: 2, color: Colors.white))
                            : const Text('Verify Account',
                                style: TextStyle(
                                    fontWeight: FontWeight.w900, fontSize: 16)),
                      ),
                    ),
                  ] else ...[
                    const Text('Step 2: Choose your role',
                        style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
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
                    if (_selectedRole == 'owner') ...[
                      const SizedBox(height: 24),
                      const Text('Business Setup',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                      const SizedBox(height: 10),
                      TextFormField(
                        controller: _businessNameController,
                        decoration: InputDecoration(
                          hintText: 'Business Name (e.g. Malkia Pharmacy)',
                          prefixIcon: const Icon(Icons.storefront_rounded),
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.black.withOpacity(0.1)),
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      const Text('Business Type',
                          style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                      const SizedBox(height: 8),
                      Wrap(
                        spacing: 8,
                        children: [
                          'pharmacy',
                          'restaurant',
                          'retail',
                          'wholesale'
                        ].map((type) {
                          final isSelected = _selectedBusinessType == type;
                          return ChoiceChip(
                            label: Text(type.substring(0, 1).toUpperCase() + type.substring(1)),
                            selected: isSelected,
                            onSelected: (val) => setState(() => _selectedBusinessType = val ? type : null),
                            selectedColor: colorScheme.primary.withOpacity(0.2),
                            labelStyle: TextStyle(
                              color: isSelected ? colorScheme.primary : Colors.black87,
                              fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                            ),
                          );
                        }).toList(),
                      ),
                    ],
                    if (_selectedRole == 'cashier') ...[
                      const SizedBox(height: 24),
                      const Text('Find your manager',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
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
                                      child: CircularProgressIndicator(strokeWidth: 2)),
                                )
                              : null,
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.black.withOpacity(0.1)),
                          ),
                        ),
                      ),
                      if (_managerSuggestions.isNotEmpty && _selectedManager == null)
                        Container(
                          margin: const EdgeInsets.only(top: 4),
                          constraints: const BoxConstraints(maxHeight: 250),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.1),
                                blurRadius: 10,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(12),
                            child: ListView.separated(
                              shrinkWrap: true,
                              padding: EdgeInsets.zero,
                              itemCount: _managerSuggestions.length,
                              separatorBuilder: (context, index) => const Divider(height: 1),
                              itemBuilder: (context, index) {
                                final m = _managerSuggestions[index];
                                return ListTile(
                                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                  leading: CircleAvatar(
                                    backgroundColor: colorScheme.primary.withOpacity(0.1),
                                    child: Text(m['name'][0].toUpperCase(),
                                        style: TextStyle(
                                            color: colorScheme.primary, fontWeight: FontWeight.bold)),
                                  ),
                                  title:
                                      Text(m['name'], style: const TextStyle(fontWeight: FontWeight.bold)),
                                  subtitle: Text("${m['business_name']}\n${m['phone']}", 
                                    style: const TextStyle(fontSize: 12, height: 1.4)),
                                  isThreeLine: true,
                                  onTap: () => setState(() {
                                    _selectedManager = m;
                                    _managerSearchController.text = m['name'];
                                    _managerSuggestions = [];
                                  }),
                                );
                              },
                            ),
                          ),
                        ),
                      if (_selectedManager != null) ...[
                        const SizedBox(height: 12),
                        Container(
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            color: colorScheme.primary.withOpacity(0.05),
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: colorScheme.primary.withOpacity(0.2)),
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
                    const SizedBox(height: 32),
                    SizedBox(
                      width: double.infinity,
                      child: FilledButton(
                        onPressed: _completeOnboarding,
                        style: FilledButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(999)),
                        ),
                        child: _loading
                            ? const SizedBox(
                                height: 18,
                                width: 18,
                                child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                            : const Text('Complete Setup',
                                style: TextStyle(fontWeight: FontWeight.w900)),
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _RoleCard extends StatelessWidget {
  const _RoleCard({
    required this.title,
    required this.icon,
    required this.selected,
    required this.onTap,
  });

  final String title;
  final IconData icon;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 8),
        decoration: BoxDecoration(
          color: selected ? colorScheme.primary : Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: selected ? colorScheme.primary : Colors.black.withOpacity(0.1),
            width: 2,
          ),
          boxShadow: [
            if (selected)
              BoxShadow(
                color: colorScheme.primary.withOpacity(0.3),
                blurRadius: 12,
                offset: const Offset(0, 4),
              ),
          ],
        ),
        child: Column(
          children: [
            Icon(
              icon,
              size: 32,
              color: selected ? Colors.white : Colors.black54,
            ),
            const SizedBox(height: 10),
            Text(
              title,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w900,
                color: selected ? Colors.white : Colors.black87,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
