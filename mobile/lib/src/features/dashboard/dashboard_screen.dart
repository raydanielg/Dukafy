import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../core/api/api_client.dart';
import '../auth/auth_repository.dart';
import '../auth/login_screen.dart';

class DashboardScreen extends ConsumerStatefulWidget {
  const DashboardScreen({super.key});

  static const routeName = 'dashboard';
  static const routePath = '/dashboard';

  @override
  ConsumerState<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends ConsumerState<DashboardScreen> {
  bool _isLoading = true;
  Map<String, dynamic>? _userData;

  @override
  void initState() {
    super.initState();
    _fetchUserData();
  }

  Future<void> _fetchUserData() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/me');
      if (mounted) {
        setState(() {
          _userData = res.data['user'];
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error loading dashboard: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    final hasBusiness = _userData?['business'] != null;
    final roles = (_userData?['roles'] as List?) ?? [];
    final isManager = roles.any((r) => r['slug'] == 'manager' || r['slug'] == 'admin');

    // If manager but no business yet, show business creation
    if (isManager && !hasBusiness) {
      return _CreateBusinessView(onCreated: _fetchUserData);
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        title: const Text('Dukafy', 
          style: TextStyle(color: Colors.black, fontWeight: FontWeight.w900, fontSize: 24)),
        actions: [
          IconButton(
            onPressed: () async {
              await ref.read(authRepositoryProvider).logout();
              if (mounted) context.go(LoginScreen.routePath);
            },
            icon: const Icon(Icons.logout_rounded, color: Colors.black),
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _fetchUserData,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildWelcomeHeader(),
              const SizedBox(height: 24),
              _buildKPICards(),
              const SizedBox(height: 24),
              const Text(
                'Quick Actions',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 12),
              _buildQuickActions(),
              const SizedBox(height: 24),
              _buildRecentTransactions(),
            ],
          ),
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 0,
        selectedItemColor: Theme.of(context).primaryColor,
        unselectedItemColor: Colors.grey,
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.dashboard_rounded), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.inventory_2_rounded), label: 'Stock'),
          BottomNavigationBarItem(icon: Icon(Icons.receipt_long_rounded), label: 'Sales'),
          BottomNavigationBarItem(icon: Icon(Icons.person_rounded), label: 'Profile'),
        ],
      ),
    );
  }

  Widget _buildWelcomeHeader() {
    final businessName = _userData?['business']?['name'] ?? 'Your Business';
    return Container(
      padding: const EdgeInsets.all(20),
      width: double.infinity,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Theme.of(context).primaryColor, Theme.of(context).primaryColor.withOpacity(0.8)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Welcome back,',
            style: TextStyle(color: Colors.white.withOpacity(0.9), fontSize: 16),
          ),
          Text(
            _userData?['name'] ?? 'User',
            style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              businessName,
              style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.w600),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildKPICards() {
    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: _KPICard(
                title: 'Today\'s Sales',
                value: 'TZS 0',
                icon: Icons.account_balance_wallet_rounded,
                color: Colors.blue,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _KPICard(
                title: 'Transactions',
                value: '0',
                icon: Icons.receipt_long_rounded,
                color: Colors.purple,
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: _KPICard(
                title: 'Low Stock',
                value: '0 Items',
                icon: Icons.warning_amber_rounded,
                color: Colors.orange,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _KPICard(
                title: 'Staff',
                value: '1 Active',
                icon: Icons.people_alt_rounded,
                color: Colors.green,
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildQuickActions() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _QuickAction(icon: Icons.add_shopping_cart_rounded, label: 'New Sale', onTap: () {}),
          _QuickAction(icon: Icons.inventory_2_outlined, label: 'Add Stock', onTap: () {}),
          _QuickAction(icon: Icons.analytics_rounded, label: 'Reports', onTap: () {}),
          _QuickAction(icon: Icons.settings_rounded, label: 'Settings', onTap: () {}),
        ],
      ),
    );
  }

  Widget _buildRecentTransactions() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text('Recent Sales', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            TextButton(onPressed: () {}, child: const Text('See All')),
          ],
        ),
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
          ),
          child: const Center(
            child: Padding(
              padding: EdgeInsets.symmetric(vertical: 20),
              child: Text('No sales recorded today.', style: TextStyle(color: Colors.grey)),
            ),
          ),
        ),
      ],
    );
  }
}

class _KPICard extends StatelessWidget {
  final String title;
  final String value;
  final IconData icon;
  final Color color;

  const _KPICard({
    required this.title,
    required this.value,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(height: 12),
          Text(value, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
          Text(title, style: TextStyle(fontSize: 12, color: Colors.grey.shade600, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}

class _QuickAction extends StatelessWidget {
  final IconData icon;
  final String label;
  final VoidCallback onTap;

  const _QuickAction({required this.icon, required this.label, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.grey.shade100,
              borderRadius: BorderRadius.circular(15),
            ),
            child: Icon(icon, color: Colors.black87),
          ),
          const SizedBox(height: 8),
          Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}

class _CreateBusinessView extends ConsumerStatefulWidget {
  final VoidCallback onCreated;
  const _CreateBusinessView({required this.onCreated});

  @override
  ConsumerState<_CreateBusinessView> createState() => _CreateBusinessViewState();
}

class _CreateBusinessViewState extends ConsumerState<_CreateBusinessView> {
  final _nameController = TextEditingController();
  String? _selectedType;
  bool _loading = false;

  final List<Map<String, String>> _types = [
    {'label': 'Pharmacy', 'slug': 'pharmacy', 'icon': '💊'},
    {'label': 'Restaurant', 'slug': 'restaurant', 'icon': '🍔'},
    {'label': 'Retail Shop', 'slug': 'retail', 'icon': '🛍️'},
    {'label': 'Wholesale', 'slug': 'wholesale', 'icon': '📦'},
  ];

  Future<void> _create() async {
    if (_nameController.text.isEmpty || _selectedType == null) return;
    setState(() => _loading = true);
    try {
      final dio = ref.read(apiClientProvider).dio;
      await dio.post('/auth/complete-onboarding', data: {
        'role': 'owner',
        'business_name': _nameController.text.trim(),
        'business_type': _selectedType,
      });
      widget.onCreated();
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Failed: $e')));
      }
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 20),
              const Text('Set Up Your Shop 🛍️', 
                style: TextStyle(fontSize: 32, fontWeight: FontWeight.w900, letterSpacing: -0.5)),
              const SizedBox(height: 12),
              const Text('Tell us about your business to get your Dukafy dashboard ready.', 
                style: TextStyle(color: Colors.grey, fontSize: 16, height: 1.5)),
              const SizedBox(height: 40),
              const Text('BUSINESS NAME', 
                style: TextStyle(fontWeight: FontWeight.w800, fontSize: 12, color: Colors.grey, letterSpacing: 1)),
              const SizedBox(height: 8),
              TextField(
                controller: _nameController,
                style: const TextStyle(fontWeight: FontWeight.bold),
                decoration: InputDecoration(
                  hintText: 'e.g. Malkia Pharmacy',
                  prefixIcon: const Icon(Icons.storefront_rounded),
                  filled: true,
                  fillColor: Colors.grey.shade50,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16), 
                    borderSide: BorderSide(color: Theme.of(context).primaryColor, width: 2)
                  ),
                ),
              ),
              const SizedBox(height: 32),
              const Text('WHAT DO YOU SELL?', 
                style: TextStyle(fontWeight: FontWeight.w800, fontSize: 12, color: Colors.grey, letterSpacing: 1)),
              const SizedBox(height: 12),
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: _types.length,
                itemBuilder: (context, index) {
                  final type = _types[index];
                  final isSelected = _selectedType == type['slug'];
                  return Padding(
                    padding: const EdgeInsets.only(bottom: 12),
                    child: InkWell(
                      onTap: () => setState(() => _selectedType = type['slug']),
                      borderRadius: BorderRadius.circular(16),
                      child: AnimatedContainer(
                        duration: const Duration(milliseconds: 200),
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: isSelected ? Theme.of(context).primaryColor : Colors.grey.shade50,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: isSelected ? Theme.of(context).primaryColor : Colors.grey.shade200,
                            width: 2
                          ),
                        ),
                        child: Row(
                          children: [
                            Text(type['icon']!, style: const TextStyle(fontSize: 24)),
                            const SizedBox(width: 16),
                            Text(
                              type['label']!,
                              style: TextStyle(
                                color: isSelected ? Colors.white : Colors.black87,
                                fontWeight: FontWeight.w700,
                                fontSize: 16
                              ),
                            ),
                            const Spacer(),
                            if (isSelected) const Icon(Icons.check_circle_rounded, color: Colors.white)
                          ],
                        ),
                      ),
                    ),
                  );
                },
              ),
              const SizedBox(height: 40),
              SizedBox(
                width: double.infinity,
                child: FilledButton(
                  onPressed: _loading ? null : _create,
                  style: FilledButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 20),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: _loading 
                    ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) 
                    : const Text('Launch Dashboard', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
