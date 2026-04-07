import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../core/api/api_client.dart';
import '../auth/auth_repository.dart';
import '../auth/login_screen.dart';
import '../auth/user_provider.dart';
import '../pos/pos_screen.dart';
import '../products/products_screen.dart';
import '../purchases/purchases_screen.dart';
import '../invoices/invoices_screen.dart';
import '../profile/profile_screen.dart';
import '../articles/articles_screen.dart';
import '../shops/shops_screen.dart';
import '../members/members_screen.dart';
import '../customers/customers_screen.dart';
import '../suppliers/suppliers_screen.dart';

class DashboardScreen extends ConsumerStatefulWidget {
  const DashboardScreen({super.key});

  static const routeName = 'dashboard';
  static const routePath = '/dashboard';

  @override
  ConsumerState<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends ConsumerState<DashboardScreen> {
  final Color primaryGreen = const Color(0xFF2E7D32);
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _fetchUserData());
  }

  Future<void> _fetchUserData() async {
    try {
      ref.read(userProvider.notifier).setLoading(true);
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/me');
      if (mounted) {
        ref.read(userProvider.notifier).setUser(res.data['user']);
      }
    } catch (e) {
      if (mounted) {
        ref.read(userProvider.notifier).setLoading(false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error loading dashboard: $e')),
        );
      }
    }
  }

  Future<void> _deleteAccount() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Delete Account?'),
        content: const Text('This action is permanent and cannot be undone. All your data will be removed.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('CANCEL')),
          TextButton(
            onPressed: () => Navigator.pop(context, true), 
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('DELETE'),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      try {
        await ref.read(authRepositoryProvider).deleteAccount();
        if (mounted) context.go(LoginScreen.routePath);
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString())));
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final userState = ref.watch(userProvider);
    final userData = userState.data;
    final isLoading = userState.isLoading;

    if (!isLoading && userData != null) {
      final hasBusiness = userData['business'] != null;
      final roles = (userData['roles'] as List?) ?? [];
      final isManager = roles.any((r) => r['slug'] == 'manager' || r['slug'] == 'admin');

      if (isManager && !hasBusiness) {
        return _CreateBusinessView(onCreated: _fetchUserData, primaryGreen: primaryGreen);
      }
    }

    return Skeletonizer(
      enabled: isLoading,
      child: Scaffold(
        key: _scaffoldKey,
        backgroundColor: const Color(0xFFF5F5F5),
        drawer: _buildDrawer(userData),
        appBar: AppBar(
          backgroundColor: primaryGreen,
          elevation: 0,
          leading: IconButton(
            icon: const Icon(Icons.menu, color: Colors.white),
            onPressed: () => _scaffoldKey.currentState?.openDrawer(),
          ),
          title: const Text('Dashboard', 
            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 20)),
          actions: [
            IconButton(
              onPressed: _fetchUserData,
              icon: const Icon(Icons.refresh, color: Colors.white),
            ),
            IconButton(
              onPressed: () {},
              icon: const Icon(Icons.settings, color: Colors.white),
            ),
            IconButton(
              onPressed: () {},
              icon: const Icon(Icons.search, color: Colors.white),
            ),
            GestureDetector(
              onTap: () => context.push(ProfileScreen.routePath),
              child: CircleAvatar(
                radius: 16,
                backgroundColor: Colors.white.withOpacity(0.2),
                backgroundImage: userData?['avatar_url'] != null 
                    ? NetworkImage(userData!['avatar_url']) 
                    : null,
                child: userData?['avatar_url'] == null 
                    ? Text(userData?['name']?[0]?.toUpperCase() ?? 'U', 
                        style: const TextStyle(color: Colors.white, fontSize: 12))
                    : null,
              ),
            ),
            const SizedBox(width: 16),
          ],
        ),
        body: RefreshIndicator(
          onRefresh: _fetchUserData,
          color: primaryGreen,
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            child: Column(
              children: [
                _buildBusinessSelector(userData),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Column(
                    children: [
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          Expanded(
                            child: _SmallKPICard(
                              title: 'Apr Sales',
                              value: '0.00',
                              icon: Icons.trending_up,
                              iconColor: primaryGreen,
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: _SmallKPICard(
                              title: 'Apr Credits',
                              value: '0.00',
                              icon: Icons.credit_card,
                              iconColor: primaryGreen,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      _buildFeatureGrid(),
                      const SizedBox(height: 100),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
        bottomNavigationBar: _buildBottomNav(),
      ),
    );
  }

  Widget _buildDrawer(Map<String, dynamic>? userData) {
    final String name = userData?['name'] ?? 'User';
    final String? avatarUrl = userData?['avatar_url'];
    final String? businessLogoUrl = userData?['business']?['logo_url'];

    return Drawer(
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.fromLTRB(20, 60, 20, 30),
            width: double.infinity,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [primaryGreen, primaryGreen.withOpacity(0.8)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            child: Column(
              children: [
                Stack(
                  alignment: Alignment.center,
                  children: [
                    GestureDetector(
                      onTap: () => context.push(ProfileScreen.routePath),
                      child: Container(
                        width: 100,
                        height: 100,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 3),
                          boxShadow: [
                            BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 10),
                          ],
                        ),
                        child: CircleAvatar(
                          backgroundColor: Colors.white,
                          backgroundImage: avatarUrl != null ? NetworkImage(avatarUrl) : null,
                          child: avatarUrl == null 
                            ? Text(name[0].toUpperCase(), style: TextStyle(fontSize: 40, fontWeight: FontWeight.bold, color: primaryGreen))
                            : null,
                        ),
                      ),
                    ),
                    Positioned(
                      bottom: 0,
                      right: 0,
                      child: GestureDetector(
                        onTap: () => context.push(ProfileScreen.routePath),
                        child: Container(
                          padding: const EdgeInsets.all(6),
                          decoration: BoxDecoration(color: Colors.white, shape: BoxShape.circle, border: Border.all(color: primaryGreen, width: 1)),
                          child: Icon(Icons.camera_alt, size: 16, color: primaryGreen),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 15),
                Text(
                  name,
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900),
                ),
                if (businessLogoUrl != null) ...[
                  const SizedBox(height: 8),
                  ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: Image.network(businessLogoUrl, height: 30, fit: BoxFit.contain),
                  ),
                ],
              ],
            ),
          ),
          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                _buildDrawerItem(Icons.dashboard_outlined, 'Dashboard', isSelected: true, onTap: () => Navigator.pop(context)),
                _buildDrawerItem(Icons.storefront_outlined, 'Shops', trailing: '2 >', onTap: () {
                  Navigator.pop(context);
                  context.push(ShopsScreen.routePath);
                }),
                _buildDrawerItem(Icons.people_outline, 'Members', trailing: '0 >', onTap: () {
                  Navigator.pop(context);
                  context.push(MembersScreen.routePath);
                }),
                _buildDrawerItem(Icons.person_search_outlined, 'Customers', trailing: '0 >', onTap: () {
                  Navigator.pop(context);
                  context.push(CustomersScreen.routePath);
                }),
                _buildDrawerItem(Icons.local_shipping_outlined, 'Suppliers', trailing: '1 >', onTap: () {
                  Navigator.pop(context);
                  context.push(SuppliersScreen.routePath);
                }),
                _buildDrawerItem(Icons.inventory_2_outlined, 'Products', trailing: '0 >', onTap: () {
                  Navigator.pop(context);
                  context.push(ProductsScreen.routePath);
                }),
                const Divider(),
                _buildDrawerItem(Icons.article_outlined, 'Articles', trailing: 'New', onTap: () {
                  Navigator.pop(context);
                  context.push(ArticlesScreen.routePath);
                }),
                const Divider(),
                ListTile(
                  leading: const Icon(Icons.person_remove_outlined, color: Colors.red),
                  title: const Text('Delete Account', style: TextStyle(color: Colors.red)),
                  onTap: () {
                    Navigator.pop(context);
                    _deleteAccount();
                  },
                ),
              ],
            ),
          ),
          const Divider(height: 1),
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 16),
            child: Row(
              children: [
                Expanded(
                  child: TextButton.icon(
                    onPressed: () {},
                    icon: const Icon(Icons.share_outlined),
                    label: const Text('Share'),
                    style: TextButton.styleFrom(foregroundColor: Colors.black87),
                  ),
                ),
                Expanded(
                  child: TextButton.icon(
                    onPressed: () async {
                      await ref.read(authRepositoryProvider).logout();
                      if (mounted) context.go(LoginScreen.routePath);
                    },
                    icon: const Icon(Icons.logout_rounded),
                    label: const Text('Logout'),
                    style: TextButton.styleFrom(foregroundColor: Colors.red),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDrawerItem(IconData icon, String title, {String? trailing, bool isSelected = false, VoidCallback? onTap}) {
    return ListTile(
      leading: Icon(icon, color: isSelected ? primaryGreen : Colors.black87),
      title: Text(title, style: TextStyle(
        color: isSelected ? primaryGreen : Colors.black87,
        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
      )),
      trailing: trailing != null ? Text(trailing, style: const TextStyle(color: Colors.grey, fontSize: 12)) : null,
      onTap: onTap ?? () => Navigator.pop(context),
    );
  }

  Widget _buildBusinessSelector(Map<String, dynamic>? userData) {
    final businessName = userData?['business']?['name'] ?? 'Select Business';
    return Container(
      width: double.infinity,
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: primaryGreen.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(Icons.storefront, color: primaryGreen),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Business', style: TextStyle(color: Colors.grey, fontSize: 12)),
                Text(businessName, 
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              ],
            ),
          ),
          const Icon(Icons.keyboard_arrow_down, color: Colors.grey),
        ],
      ),
    );
  }

  Widget _buildFeatureGrid() {
    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: 2,
      mainAxisSpacing: 12,
      crossAxisSpacing: 12,
      childAspectRatio: 0.95,
      children: [
        _FeatureCard(title: 'Customers', subtitle: 'Manage Borrowers, clients & profiles', icon: Icons.people_outline, color: primaryGreen, onTap: () => context.push(CustomersScreen.routePath)),
        _FeatureCard(title: 'Loans & Repayments', subtitle: 'Applications & active loans', icon: Icons.grid_view_rounded, color: primaryGreen),
        _FeatureCard(title: 'Expenses', subtitle: 'Operational costs', icon: Icons.payments_outlined, color: primaryGreen),
        _FeatureCard(title: 'Treasury & Banking', subtitle: 'Manage teller, Customers and Debts', icon: Icons.account_balance_wallet_outlined, color: primaryGreen),
        _FeatureCard(title: 'SMS & Emails', subtitle: 'Notifications & Alerts', icon: Icons.mail_outline, color: primaryGreen),
        _FeatureCard(title: 'Reports', subtitle: 'Business Analytics', icon: Icons.bar_chart_rounded, color: primaryGreen),
      ],
    );
  }

  Widget _buildBottomNav() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)],
      ),
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 8),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _NavButton(icon: Icons.dashboard_rounded, label: 'Home', isSelected: true, color: primaryGreen, onTap: () {}),
              _NavButton(icon: Icons.point_of_sale_rounded, label: 'POS', color: primaryGreen, onTap: () => context.push(POSScreen.routePath)),
              _NavButton(icon: Icons.inventory_2_outlined, label: 'Product', color: primaryGreen, onTap: () => context.push(ProductsScreen.routePath)),
              _NavButton(icon: Icons.shopping_cart_outlined, label: 'Purchase', color: primaryGreen, onTap: () => context.push(PurchasesScreen.routePath)),
              _NavButton(icon: Icons.person_rounded, label: 'Profile', color: primaryGreen, onTap: () => context.push(ProfileScreen.routePath)),
            ],
          ),
        ),
      ),
    );
  }
}

class _SmallKPICard extends StatelessWidget {
  final String title;
  final String value;
  final IconData icon;
  final Color iconColor;
  const _SmallKPICard({required this.title, required this.value, required this.icon, required this.iconColor});
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 5)]),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Container(padding: const EdgeInsets.all(6), decoration: BoxDecoration(color: iconColor.withOpacity(0.1), borderRadius: BorderRadius.circular(6)), child: Icon(icon, color: iconColor, size: 18)),
          const SizedBox(width: 8),
          Text(title, style: const TextStyle(color: Colors.grey, fontSize: 12)),
        ]),
        const SizedBox(height: 8),
        Text(value, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
      ]),
    );
  }
}

class _FeatureCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final IconData icon;
  final Color color;
  final VoidCallback? onTap;
  const _FeatureCard({required this.title, required this.subtitle, required this.icon, required this.color, this.onTap});
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 8)]),
        child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
          Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: color.withOpacity(0.1), shape: BoxShape.circle), child: Icon(icon, color: color, size: 28)),
          const SizedBox(height: 12),
          Text(title, textAlign: TextAlign.center, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
          const SizedBox(height: 4),
          Text(subtitle, textAlign: TextAlign.center, style: const TextStyle(color: Colors.grey, fontSize: 10), maxLines: 2, overflow: TextOverflow.ellipsis),
        ]),
      ),
    );
  }
}

class _NavButton extends StatelessWidget {
  final IconData icon;
  final String label;
  final bool isSelected;
  final Color color;
  final VoidCallback? onTap;
  const _NavButton({required this.icon, required this.label, this.isSelected = false, required this.color, this.onTap});
  @override
  Widget build(BuildContext context) {
    return GestureDetector(onTap: onTap, child: Column(mainAxisSize: MainAxisSize.min, children: [
      Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: isSelected ? color : Colors.transparent, borderRadius: BorderRadius.circular(8)), child: Icon(icon, color: isSelected ? Colors.white : Colors.grey.shade600, size: 24)),
      const SizedBox(height: 4),
      Text(label, style: TextStyle(fontSize: 10, fontWeight: isSelected ? FontWeight.bold : FontWeight.normal, color: isSelected ? color : Colors.grey.shade600)),
    ]));
  }
}

class _CreateBusinessView extends ConsumerStatefulWidget {
  final VoidCallback onCreated;
  final Color primaryGreen;
  const _CreateBusinessView({required this.onCreated, required this.primaryGreen});
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
      await dio.post('/auth/complete-onboarding', data: {'role': 'owner', 'business_name': _nameController.text.trim(), 'business_type': _selectedType});
      widget.onCreated();
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Failed: $e')));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(child: SingleChildScrollView(padding: const EdgeInsets.all(24), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        const SizedBox(height: 20),
        Text('Set Up Your Shop 🛍️', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w900, color: widget.primaryGreen)),
        const SizedBox(height: 12),
        const Text('Tell us about your business to get your Dukafy dashboard ready.', style: TextStyle(color: Colors.grey, fontSize: 16, height: 1.5)),
        const SizedBox(height: 40),
        const Text('BUSINESS NAME', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 12, color: Colors.grey, letterSpacing: 1)),
        const SizedBox(height: 8),
        TextField(controller: _nameController, decoration: InputDecoration(hintText: 'e.g. Malkia Pharmacy', prefixIcon: Icon(Icons.storefront_rounded, color: widget.primaryGreen), filled: true, fillColor: Colors.grey.shade50, border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none), focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: widget.primaryGreen, width: 2)))),
        const SizedBox(height: 32),
        const Text('WHAT DO YOU SELL?', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 12, color: Colors.grey, letterSpacing: 1)),
        const SizedBox(height: 12),
        ListView.builder(shrinkWrap: true, physics: const NeverScrollableScrollPhysics(), itemCount: _types.length, itemBuilder: (context, index) {
          final type = _types[index];
          final isSelected = _selectedType == type['slug'];
          return Padding(padding: const EdgeInsets.only(bottom: 12), child: InkWell(onTap: () => setState(() => _selectedType = type['slug']), borderRadius: BorderRadius.circular(16), child: AnimatedContainer(duration: const Duration(milliseconds: 200), padding: const EdgeInsets.all(16), decoration: BoxDecoration(color: isSelected ? widget.primaryGreen : Colors.grey.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: isSelected ? widget.primaryGreen : Colors.grey.shade200, width: 2)), child: Row(children: [Text(type['icon']!, style: const TextStyle(fontSize: 24)), const SizedBox(width: 16), Text(type['label']!, style: TextStyle(color: isSelected ? Colors.white : Colors.black87, fontWeight: FontWeight.bold, fontSize: 16)), const Spacer(), if (isSelected) const Icon(Icons.check_circle_rounded, color: Colors.white)]))));
        }),
        const SizedBox(height: 40),
        SizedBox(width: double.infinity, child: FilledButton(onPressed: _loading ? null : _create, style: FilledButton.styleFrom(backgroundColor: widget.primaryGreen, padding: const EdgeInsets.symmetric(vertical: 20), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16))), child: _loading ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : const Text('Launch Dashboard', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)))),
      ]))),
    );
  }
}
