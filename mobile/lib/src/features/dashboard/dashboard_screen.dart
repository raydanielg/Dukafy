import 'dart:async';

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

import '../loans/loans_screen.dart';
import '../expenses/expenses_screen.dart';
import '../banking/banking_screen.dart';
import '../notifications/notifications_screen.dart';
import '../reports/reports_screen.dart';
import '../auth/widgets/auth_background.dart';

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

  // KPI Data
  Map<String, dynamic> _kpiData = {};
  bool _kpiLoading = true;

  // Carousel controller for auto-scroll
  final PageController _kpiPageController = PageController(viewportFraction: 0.45);
  Timer? _autoScrollTimer;
  int _currentKpiPage = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchUserData();
      _fetchKpiData();
      _startAutoScroll();
    });
  }

  @override
  void dispose() {
    _autoScrollTimer?.cancel();
    _kpiPageController.dispose();
    super.dispose();
  }

  void _startAutoScroll() {
    _autoScrollTimer?.cancel();
    _autoScrollTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
      if (_kpiPageController.hasClients && mounted) {
        final nextPage = (_currentKpiPage + 1) % 6; // 6 KPI cards
        _kpiPageController.animateToPage(
          nextPage,
          duration: const Duration(milliseconds: 600),
          curve: Curves.easeInOut,
        );
      }
    });
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

  Future<void> _fetchKpiData() async {
    setState(() => _kpiLoading = true);
    try {
      final dio = ref.read(apiClientProvider).dio;

      // Fetch all dashboard data in parallel
      final responses = await Future.wait([
        dio.get('/dashboard/stats').timeout(const Duration(seconds: 10)),
        dio.get('/dashboard/summary').timeout(const Duration(seconds: 10)),
        dio.get('/sales/today').timeout(const Duration(seconds: 10)),
        dio.get('/inventory/value').timeout(const Duration(seconds: 10)),
        dio.get('/credits/outstanding').timeout(const Duration(seconds: 10)),
        dio.get('/expenses/total').timeout(const Duration(seconds: 10)),
      ]);

      if (mounted) {
        final stats = responses[0].data['data'] ?? {};
        final summary = responses[1].data['data'] ?? {};
        final sales = responses[2].data['data'] ?? {};
        final inventory = responses[3].data['data'] ?? {};
        final credits = responses[4].data['data'] ?? {};
        final expenses = responses[5].data['data'] ?? {};

        setState(() {
          _kpiData = {
            'stock_in': inventory['total_value'] ?? inventory['stock_value'] ?? 0,
            'profit': summary['profit'] ?? stats['profit'] ?? 0,
            'orders': sales['order_count'] ?? sales['orders'] ?? 0,
            'credits': credits['total_outstanding'] ?? credits['amount'] ?? 0,
            'expenses': expenses['total'] ?? expenses['amount'] ?? 0,
            'sales': sales['total_sales'] ?? sales['amount'] ?? stats['sales'] ?? 0,
            'balance': summary['balance'] ?? stats['balance'] ?? 0,
            'today_sales': sales['today'] ?? 0,
            'month_sales': sales['this_month'] ?? 0,
          };
          _kpiLoading = false;
        });
      }
    } catch (e) {
      // Try single endpoint if multiple fail
      try {
        final dio = ref.read(apiClientProvider).dio;
        final res = await dio.get('/dashboard/kpi').timeout(const Duration(seconds: 10));
        if (mounted) {
          setState(() {
            _kpiData = res.data['data'] ?? res.data ?? {};
            _kpiLoading = false;
          });
        }
      } catch (_) {
        // Use fallback data if all APIs fail
        if (mounted) {
          setState(() {
            _kpiData = {
              'stock_in': 0,
              'profit': 0,
              'orders': 0,
              'credits': 0,
              'expenses': 0,
              'sales': 0,
              'balance': 0,
            };
            _kpiLoading = false;
          });
        }
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
          onRefresh: () async {
            await _fetchUserData();
            await _fetchKpiData();
          },
          color: primaryGreen,
          child: Stack(
            children: [
              // Background with subtle lines pattern
              const AuthBackground(),
              SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  children: [
                    // Main Balance Card (like screenshot)
                    _buildBalanceCard(userData),
                    const SizedBox(height: 20),
                    // KPI Cards Section
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Section Title
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text(
                                'Overview',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w900,
                                  color: Colors.black87,
                                ),
                              ),
                              TextButton(
                                onPressed: () {},
                                child: const Text('See all'),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          // KPI Carousel with auto-scroll
                          SizedBox(
                            height: 140,
                            child: Skeletonizer(
                              enabled: _kpiLoading,
                              child: PageView.builder(
                                controller: _kpiPageController,
                                onPageChanged: (index) => setState(() => _currentKpiPage = index),
                                padEnds: false,
                                itemCount: 6,
                                itemBuilder: (context, index) {
                                  final kpiItems = [
                                    {
                                      'title': 'Stock In',
                                      'value': 'TSh ${_kpiData['stock_in'] ?? 0}',
                                      'subtitle': 'Inventory value',
                                      'icon': Icons.inventory_2,
                                      'color': Colors.blue,
                                    },
                                    {
                                      'title': 'Profit',
                                      'value': '${_kpiData['profit'] ?? 0}',
                                      'subtitle': 'Net profit',
                                      'icon': Icons.trending_up,
                                      'color': Colors.green,
                                    },
                                    {
                                      'title': 'Orders',
                                      'value': 'TSh ${_kpiData['orders'] ?? 0}',
                                      'subtitle': 'Total orders',
                                      'icon': Icons.shopping_cart,
                                      'color': Colors.purple,
                                    },
                                    {
                                      'title': 'Credits',
                                      'value': 'TSh ${_kpiData['credits'] ?? 2000000}',
                                      'subtitle': 'Outstanding',
                                      'icon': Icons.credit_card,
                                      'color': Colors.orange,
                                    },
                                    {
                                      'title': 'Expense',
                                      'value': 'TSh ${_kpiData['expenses'] ?? 2000000}',
                                      'subtitle': 'Total expenses',
                                      'icon': Icons.receipt_long,
                                      'color': Colors.red,
                                    },
                                    {
                                      'title': 'Sales',
                                      'value': 'TSh ${_kpiData['sales'] ?? 0}',
                                      'subtitle': 'Total sales',
                                      'icon': Icons.point_of_sale,
                                      'color': primaryGreen,
                                    },
                                  ];
                                  final item = kpiItems[index];
                                  return Padding(
                                    padding: const EdgeInsets.only(right: 12),
                                    child: _KPICard(
                                      title: item['title'] as String,
                                      value: item['value'] as String,
                                      subtitle: item['subtitle'] as String,
                                      icon: item['icon'] as IconData,
                                      color: item['color'] as Color,
                                      isPositive: (item['color'] as Color) != Colors.red && (item['color'] as Color) != Colors.orange,
                                    ),
                                  );
                                },
                              ),
                            ),
                          ),
                          // Page Indicator Dots
                          const SizedBox(height: 12),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: List.generate(6, (index) {
                              return AnimatedContainer(
                                duration: const Duration(milliseconds: 300),
                                margin: const EdgeInsets.symmetric(horizontal: 3),
                                height: 6,
                                width: _currentKpiPage == index ? 18 : 6,
                                decoration: BoxDecoration(
                                  color: _currentKpiPage == index
                                      ? primaryGreen
                                      : Colors.grey.shade300,
                                  borderRadius: BorderRadius.circular(3),
                                ),
                              );
                            }),
                          ),
                          const SizedBox(height: 24),
                          // Quick Actions Grid
                          const Text(
                            'Quick Actions',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w900,
                              color: Colors.black87,
                            ),
                          ),
                          const SizedBox(height: 12),
                          _buildFeatureGrid(),
                          const SizedBox(height: 100),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ],
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

  Widget _buildBalanceCard(Map<String, dynamic>? userData) {
    final businessName = userData?['business']?['name'] ?? 'My Business';
    return Container(
      width: double.infinity,
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 20,
            spreadRadius: 2,
            offset: const Offset(0, 8),
          ),
        ],
        border: Border.all(color: Colors.black.withOpacity(0.05)),
      ),
      child: Column(
        children: [
          // Business Selector Row
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: primaryGreen.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(Icons.storefront, color: primaryGreen, size: 22),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Active Business',
                      style: TextStyle(color: Colors.grey, fontSize: 12, fontWeight: FontWeight.w600),
                    ),
                    Text(
                      businessName,
                      style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.grey.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.keyboard_arrow_down, color: Colors.grey, size: 20),
              ),
            ],
          ),
          const Divider(height: 32),
          // Balance Display (like screenshot)
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.account_balance_wallet_outlined, color: Colors.grey.shade400, size: 20),
              const SizedBox(width: 8),
              const Text(
                'Balance',
                style: TextStyle(color: Colors.grey, fontSize: 14, fontWeight: FontWeight.w600),
              ),
            ],
          ),
          const SizedBox(height: 8),
          const Text(
            'TZS 0.00',
            style: TextStyle(
              fontSize: 36,
              fontWeight: FontWeight.w900,
              color: Colors.black87,
              letterSpacing: -0.5,
            ),
          ),
          const SizedBox(height: 4),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.trending_flat, color: Colors.grey.shade400, size: 16),
              const SizedBox(width: 4),
              Text(
                '0% from last week',
                style: TextStyle(color: Colors.grey.shade500, fontSize: 13),
              ),
            ],
          ),
          const SizedBox(height: 20),
          // Quick Action Buttons
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              _buildQuickActionButton(
                icon: Icons.add,
                label: 'New Sale',
                onTap: () => context.push(POSScreen.routePath),
              ),
              const SizedBox(width: 16),
              _buildQuickActionButton(
                icon: Icons.credit_card,
                label: 'Payment',
                onTap: () {},
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActionButton({
    required IconData icon,
    required String label,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        decoration: BoxDecoration(
          color: primaryGreen.withOpacity(0.1),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: primaryGreen.withOpacity(0.2)),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, color: primaryGreen, size: 18),
            const SizedBox(width: 8),
            Text(
              label,
              style: TextStyle(
                color: primaryGreen,
                fontWeight: FontWeight.w700,
                fontSize: 13,
              ),
            ),
          ],
        ),
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
        _FeatureCard(
          title: 'Customers',
          subtitle: 'Manage Borrowers, clients & profiles',
          icon: Icons.people_outline,
          color: primaryGreen,
          onTap: () => context.push(CustomersScreen.routePath),
        ),
        _FeatureCard(
          title: 'Loans & Repayments',
          subtitle: 'Applications & active loans',
          icon: Icons.grid_view_rounded,
          color: primaryGreen,
          onTap: () => context.push(LoansScreen.routePath),
        ),
        _FeatureCard(
          title: 'Expenses',
          subtitle: 'Operational costs',
          icon: Icons.payments_outlined,
          color: primaryGreen,
          onTap: () => context.push(ExpensesScreen.routePath),
        ),
        _FeatureCard(
          title: 'Treasury & Banking',
          subtitle: 'Manage teller, Customers and Debts',
          icon: Icons.account_balance_wallet_outlined,
          color: primaryGreen,
          onTap: () => context.push(BankingScreen.routePath),
        ),
        _FeatureCard(
          title: 'SMS & Emails',
          subtitle: 'Notifications & Alerts',
          icon: Icons.mail_outline,
          color: primaryGreen,
          onTap: () => context.push(NotificationsScreen.routePath),
        ),
        _FeatureCard(
          title: 'Reports',
          subtitle: 'Business Analytics',
          icon: Icons.bar_chart_rounded,
          color: primaryGreen,
          onTap: () => context.push(ReportsScreen.routePath),
        ),
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

class _KPICard extends StatelessWidget {
  final String title;
  final String value;
  final String subtitle;
  final IconData icon;
  final Color color;
  final bool isPositive;
  const _KPICard({required this.title, required this.value, required this.subtitle, required this.icon, required this.color, required this.isPositive});
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 12,
            spreadRadius: 1,
            offset: const Offset(0, 4),
          ),
        ],
        border: Border.all(color: Colors.black.withOpacity(0.05)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header with icon
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(icon, color: color, size: 18),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  title,
                  style: TextStyle(
                    color: Colors.grey.shade600,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          // Value
          Text(
            value,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w900,
              color: Colors.black87,
            ),
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 4),
          // Subtitle with trend indicator
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(
                isPositive ? Icons.trending_up : Icons.trending_down,
                color: isPositive ? Colors.green : Colors.red,
                size: 12,
              ),
              const SizedBox(width: 3),
              Flexible(
                child: Text(
                  subtitle,
                  style: TextStyle(
                    color: isPositive ? Colors.green : Colors.red,
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ],
      ),
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
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 12,
              spreadRadius: 1,
              offset: const Offset(0, 4),
            ),
          ],
          border: Border.all(color: Colors.black.withOpacity(0.04)),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 26),
            ),
            const SizedBox(height: 12),
            Text(
              title,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontWeight: FontWeight.w800,
                fontSize: 14,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              subtitle,
              textAlign: TextAlign.center,
              style: TextStyle(
                color: Colors.grey.shade500,
                fontSize: 11,
                fontWeight: FontWeight.w500,
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
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
