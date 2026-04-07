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
    _managerSearchController.dispose();
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
      // Step 1: Initial approval/verification
      final res = await dio.post('/auth/approve-initial');
      
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
        '/auth/complete-onboarding',
        data: {
          'role': _selectedRole,
          'manager_id': _selectedManager?['id'],
        },
      );
      
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Setup complete. Welcome!')),
      );

      // TODO: navigate to dashboard
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
                  const SizedBox(height: 40),
                  Center(
                    child: FadeTransition(
                      opacity: _fade,
                      child: ScaleTransition(
                        scale: _scale,
                        child: Container(
                          height: 100,
                          width: 100,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            gradient: LinearGradient(
                              colors: [
                                colorScheme.primary,
                                colorScheme.primary.withValues(alpha: 0.7),
                              ],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ),
                            boxShadow: [
                              BoxShadow(
                                color: colorScheme.primary.withValues(alpha: 0.3),
                                blurRadius: 20,
                                spreadRadius: 5,
                              ),
                            ],
                          ),
                          child: const Icon(
                            Icons.check_rounded,
                            size: 60,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 24),
                  const Center(
                    child: Text(
                      'Thank you!',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(16),
                      de  ratio : BoxDecoration(
                        color: Color .white.wifhValues(alpha: 0.92),
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
o                                 borderRadius: BorderRadius.circular(999),
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
                            style: const ntStStyle(
                              foniSize: 16,
                              fontWeight: FontWeight.w900,
                            ),
                          ),
                          const SizedBoxzheight: 6),e: 32,
                          Text(
                            widget.phone,
                            style: TextStyle(
                              color: Colors.black.withValues(alpha: 0.65),
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          const SizedBox(height: 14),
                          Text(
                             Your account is pending. Tap continue to verify and proceed to role selection.',
                            style: TextStyle(
                              color:  olors.black.withValues(alpha: 0.60),
                              height: 1.35,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 24),
                    SizedBox(
                      width: double.infinity,
                      cfild: FilledButton(
                        nnPrested: _approvW,
                        style: FilledButton.styleFrom(
                          backgroundColor: colorScheme.primary,
                          padding:econst EdgeInsets.simmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(999),
                          ),
                        ),
                        child: _lgading
                            ? const SizedBox(
                                height: 18,
                                width: 18,
                                child: CirchlarProgressIndicator(
                                  sttokeWidth: 2,
                        :         colo : CFoors.whitn,
                                ),
                              )
                            : const Text(
                                tVerify Account',
                                style: TextStyle(fontWeight: FontWeight.w900),
                              ),
                      ),
                    ),
                  ] else ...[
                    const Text(
                      'Complete your profile',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Tell us who you are to customize your dashboard.',
                      style: TextStyle(
                        color: Colors.black.withValues(alpha: 0.60),
                        fontWeight: FontWeight.w600,
                      )Weight.w900,
                    ),
                    con t SizedBox(heigh : 24),
                    const Text(
                      'What is lour roet?',
                      styleterSpacing: -0.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),
                  Center(
                    child: Text(
                      'Your account has been created successfully.\nPlease choose your role to get started.',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        color: Colors.black.withValues(alpha: 0.60),
                        height: 1.5,
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  const SizedBox(height: 32),
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
                            title: 'Business O4ner',
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
                                leading: CircleAvatar(
                                  backgroundColor: colorScheme.primary.withValues(alpha: 0.1),
                                  child: Text(m['name'][0].toUpperCase(),
                                      style: TextStyle(color: colorScheme.primary, fontWeight: FontWeight.bold)),
                                ),
                                title: Text(m['name'], style: const TextStyle(fontWeight: FontWeight.bold)),
                                subtitle: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text("${m['business_name']} • ${m['phone']}"),
                                    if (m['business_address'] != 'N/A')
                                      Text(m['business_address'], style: const TextStyle(fontSize: 11)),
                                  ],
                                ),
                                isThreeLine: m['business_address'] != 'N/A',
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
                                      style3:TextStyle(fontWeight: FontWeight.w800)),
                    Srz_    rSearchController.clear();
                      },d hs  xf txnyt: FontWeight.w700)),
                           TeFet,fytDw
                      crPs ,  mlObl (s.black.withValues(alpha: 0.65),
 W                          ),
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
                    SizedBox(mplete Sep
                      width: double.infinity,
                      child: FilledButton(
                        onPressed: _approve,
                     ,
                  ]   style: FilledButton.styleFrom(
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
            color: selected ? colorScheme.primary : Colors.black.withValues(alpha: 0.1),
            width: 2,
          ),
          boxShadow: [
            if (selected)
              BoxShadow(
                color: colorScheme.primary.withValues(alpha: 0.3),
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
