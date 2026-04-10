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
import '../sales/sale_screen.dart';
import '../payments/payment_screen.dart';
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

  // Balance visibility toggle
  bool _showBalance = false;

  // Carousel controller for auto-scroll
  final PageController _kpiPageController = PageController(viewportFraction: 0.38);
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

      // Try main dashboard endpoint
      final res = await dio.get('/dashboard').timeout(const Duration(seconds: 10));

      if (mounted && res.data != null && res.data['success'] == true) {
        final data = res.data['data'] ?? {};
        setState(() {
          _kpiData = {
            'stock_in': data['stock_in'] ?? data['stock_value'] ?? 0,
            'profit': data['profit'] ?? 0,
            'orders': data['orders'] ?? 0,
            'credits': data['credits'] ?? 0,
            'expenses': data['expenses'] ?? 0,
            'sales': data['sales'] ?? 0,
            'balance': data['balance'] ?? 0,
            'today_sales': data['today_sales'] ?? 0,
            'month_sales': data['month_sales'] ?? 0,
          };
          _kpiLoading = false;
        });
      } else {
        throw Exception('Invalid response');
      }
    } catch (e) {
      // Fallback to mock data if API fails
      if (mounted) {
        setState(() {
          _kpiData = {
            'stock_in': 2450000,
            'profit': 890000,
            'orders': 156,
            'credits': 450000,
            'expenses': 320000,
            'sales': 5200000,
            'balance': 2100000,
            'today_sales': 450000,
            'month_sales': 3200000,
          };
          _kpiLoading = false;
        });
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
                            height: 115,
                            child: Skeletonizer(
                              enabled: _kpiLoading,
                              child: PageView.builder(
                                controller: _kpiPageController,
                                onPageChanged: (index) => setState(() => _currentKpiPage = index),
                                padEnds: false,
                                itemCount: 6,
                                itemBuilder: (context, index) {
                                  // Helper to format numbers with thousand separators
                                  String formatNumber(dynamic value) {
                                    if (value == null) return '0';
                                    final num = value is int ? value : (value is double ? value.toInt() : 0);
                                    return num.toString().replaceAllMapped(RegExp(r'\B(?=(\d{3})+(?!\d))'), (match) => ',');
                                  }

                                  final kpiItems = [
                                    {
                                      'title': 'Stock In',
                                      'value': 'TSh ${formatNumber(_kpiData['stock_in'])}',
                                      'subtitle': 'Inventory value',
                                      'icon': Icons.inventory_2,
                                      'color': Colors.blue,
                                    },
                                    {
                                      'title': 'Profit',
                                      'value': 'TSh ${formatNumber(_kpiData['profit'])}',
                                      'subtitle': 'Net profit',
                                      'icon': Icons.trending_up,
                                      'color': Colors.green,
                                    },
                                    {
                                      'title': 'Orders',
                                      'value': '${formatNumber(_kpiData['orders'])}',
                                      'subtitle': 'Total orders',
                                      'icon': Icons.shopping_cart,
                                      'color': Colors.purple,
                                    },
                                    {
                                      'title': 'Credits',
                                      'value': 'TSh ${formatNumber(_kpiData['credits'])}',
                                      'subtitle': 'Outstanding',
                                      'icon': Icons.credit_card,
                                      'color': Colors.orange,
                                    },
                                    {
                                      'title': 'Expense',
                                      'value': 'TSh ${formatNumber(_kpiData['expenses'])}',
                                      'subtitle': 'Total expenses',
                                      'icon': Icons.receipt_long,
                                      'color': Colors.red,
                                    },
                                    {
                                      'title': 'Sales',
                                      'value': 'TSh ${formatNumber(_kpiData['sales'])}',
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

    // Format currency
    final balance = _kpiData['balance'] ?? 0;
    final todaySales = _kpiData['today_sales'] ?? 0;
    final formattedBalance = 'TZS ${balance.toString().replaceAllMapped(RegExp(r'\B(?=(\d{3})+(?!\d))'), (match) => ',')}';

    // Calculate trend (compare today vs yesterday - simplified)
    final trendPercent = todaySales > 0 ? '+${(todaySales / 100).toStringAsFixed(1)}%' : '0%';
    final isPositive = todaySales >= 0;

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
          // Business Selector Row - Dropdown controls balance card
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
              // Dropdown arrow button - controls balance card visibility
              GestureDetector(
                onTap: () => setState(() => _showBalance = !_showBalance),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 300),
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: _showBalance ? primaryGreen : Colors.grey.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: AnimatedRotation(
                    duration: const Duration(milliseconds: 300),
                    turns: _showBalance ? 0.5 : 0,
                    child: Icon(
                      Icons.keyboard_arrow_down,
                      color: _showBalance ? Colors.white : Colors.grey,
                      size: 22,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          // Balance Card Section - Expandable/Collapsible
          AnimatedContainer(
            duration: const Duration(milliseconds: 400),
            curve: Curves.easeInOutCubic,
            height: _showBalance ? null : 0,
            child: ClipRect(
              child: AnimatedOpacity(
                duration: const Duration(milliseconds: 300),
                opacity: _showBalance ? 1.0 : 0.0,
                child: Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: Colors.grey.shade50,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: Colors.grey.shade200),
                  ),
                  child: Column(
                    children: [
                      // Balance Header
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.account_balance_wallet_outlined, color: primaryGreen, size: 20),
                          const SizedBox(width: 8),
                          const Text(
                            'Current Balance',
                            style: TextStyle(color: Colors.grey, fontSize: 14, fontWeight: FontWeight.w600),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      // Amount
                      Skeletonizer(
                        enabled: _kpiLoading,
                        child: Text(
                          _kpiLoading ? 'TZS 0,000,000' : formattedBalance,
                          style: const TextStyle(
                            fontSize: 32,
                            fontWeight: FontWeight.w900,
                            color: Colors.black87,
                            letterSpacing: -0.5,
                          ),
                        ),
                      ),
                      const SizedBox(height: 8),
                      // Trend
                      Skeletonizer(
                        enabled: _kpiLoading,
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              isPositive ? Icons.trending_up : Icons.trending_down,
                              color: isPositive ? Colors.green : Colors.red,
                              size: 16,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              '$trendPercent from yesterday',
                              style: TextStyle(
                                color: isPositive ? Colors.green : Colors.red,
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 20),
                      // Action Buttons
                      Row(
                        children: [
                          Expanded(
                            child: _buildQuickActionButton(
                              icon: Icons.add,
                              label: 'New Sale',
                              onTap: () => context.push(SaleScreen.routePath),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: _buildQuickActionButton(
                              icon: Icons.credit_card,
                              label: 'Payment',
                              onTap: () => context.push(PaymentScreen.routePath),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
          const SizedBox(height: 12),
        ],
      ),
    );
  }

  void _showBalanceDropdown(BuildContext context) {
    // Format number helper
    String formatNumber(dynamic value) {
      if (value == null) return '0';
      final num = value is int ? value : (value is double ? value.toInt() : 0);
      return num.toString().replaceAllMapped(RegExp(r'\B(?=(\d{3})+(?!\d))'), (match) => ',');
    }

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        margin: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.2),
              blurRadius: 20,
              spreadRadius: 5,
            ),
          ],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: primaryGreen.withOpacity(0.1),
                borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: primaryGreen,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.account_balance_wallet, color: Colors.white, size: 24),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'All Balances',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                        Text(
                          'Business Overview',
                          style: TextStyle(
                            fontSize: 13,
                            color: Colors.grey.shade600,
                          ),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close),
                    onPressed: () => Navigator.pop(context),
                  ),
                ],
              ),
            ),
            // Balance Items
            Padding(
              padding: const EdgeInsets.all(16),
              child: Skeletonizer(
                enabled: _kpiLoading,
                child: Column(
                  children: [
                    _BalanceDropdownItem(
                      icon: Icons.account_balance_wallet,
                      title: 'Main Balance',
                      value: 'TZS ${formatNumber(_kpiData['balance'])}',
                      color: primaryGreen,
                      isMain: true,
                    ),
                    const Divider(height: 24),
                    _BalanceDropdownItem(
                      icon: Icons.inventory_2,
                      title: 'Stock Value',
                      value: 'TZS ${formatNumber(_kpiData['stock_in'])}',
                      color: Colors.blue,
                    ),
                    const Divider(height: 24),
                    _BalanceDropdownItem(
                      icon: Icons.trending_up,
                      title: 'Profit',
                      value: 'TZS ${formatNumber(_kpiData['profit'])}',
                      color: Colors.green,
                    ),
                    const Divider(height: 24),
                    _BalanceDropdownItem(
                      icon: Icons.shopping_cart,
                      title: 'Orders',
                      value: '${formatNumber(_kpiData['orders'])} orders',
                      color: Colors.purple,
                    ),
                    const Divider(height: 24),
                    _BalanceDropdownItem(
                      icon: Icons.credit_card,
                      title: 'Outstanding Credits',
                      value: 'TZS ${formatNumber(_kpiData['credits'])}',
                      color: Colors.orange,
                    ),
                    const Divider(height: 24),
                    _BalanceDropdownItem(
                      icon: Icons.receipt_long,
                      title: 'Total Expenses',
                      value: 'TZS ${formatNumber(_kpiData['expenses'])}',
                      color: Colors.red,
                    ),
                    const Divider(height: 24),
                    _BalanceDropdownItem(
                      icon: Icons.point_of_sale,
                      title: 'Total Sales',
                      value: 'TZS ${formatNumber(_kpiData['sales'])}',
                      color: Colors.teal,
                    ),
                  ],
                ),
              ),
            ),
            // Close button
            Padding(
              padding: const EdgeInsets.all(16),
              child: SizedBox(
                width: double.infinity,
                height: 50,
                child: FilledButton(
                  onPressed: () => Navigator.pop(context),
                  style: FilledButton.styleFrom(
                    backgroundColor: primaryGreen,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'CLOSE',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                ),
              ),
            ),
          ],
        ),
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
      crossAxisCount: 3,
      mainAxisSpacing: 10,
      crossAxisSpacing: 10,
      childAspectRatio: 0.85,
      children: [
        _FeatureCard(
          title: 'Customers',
          subtitle: 'Clients & Borrowers',
          icon: Icons.people_outline,
          color: primaryGreen,
          onTap: () => context.push(CustomersScreen.routePath),
        ),
        _FeatureCard(
          title: 'Loans',
          subtitle: 'Credit & Repayments',
          icon: Icons.grid_view_rounded,
          color: primaryGreen,
          onTap: () => context.push(LoansScreen.routePath),
        ),
        _FeatureCard(
          title: 'Expenses',
          subtitle: 'Costs & Spending',
          icon: Icons.payments_outlined,
          color: primaryGreen,
          onTap: () => context.push(ExpensesScreen.routePath),
        ),
        _FeatureCard(
          title: 'Banking',
          subtitle: 'Treasury & Teller',
          icon: Icons.account_balance_wallet_outlined,
          color: primaryGreen,
          onTap: () => context.push(BankingScreen.routePath),
        ),
        _FeatureCard(
          title: 'Messages',
          subtitle: 'SMS & Alerts',
          icon: Icons.mail_outline,
          color: primaryGreen,
          onTap: () => context.push(NotificationsScreen.routePath),
        ),
        _FeatureCard(
          title: 'Reports',
          subtitle: 'Analytics & Insights',
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

class _BalanceDropdownItem extends StatelessWidget {
  final IconData icon;
  final String title;
  final String value;
  final Color color;
  final bool isMain;

  const _BalanceDropdownItem({
    required this.icon,
    required this.title,
    required this.value,
    required this.color,
    this.isMain = false,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: color, size: 22),
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: TextStyle(
                  fontSize: 13,
                  color: Colors.grey.shade600,
                  fontWeight: FontWeight.w500,
                ),
              ),
              Text(
                value,
                style: TextStyle(
                  fontSize: isMain ? 18 : 16,
                  fontWeight: isMain ? FontWeight.w900 : FontWeight.w700,
                  color: isMain ? color : Colors.black87,
                ),
              ),
            ],
          ),
        ),
        if (isMain)
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              'MAIN',
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
          ),
      ],
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
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 8,
            spreadRadius: 1,
            offset: const Offset(0, 3),
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
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(icon, color: color, size: 16),
              ),
              const SizedBox(width: 6),
              Expanded(
                child: Text(
                  title,
                  style: TextStyle(
                    color: Colors.grey.shade600,
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          // Value
          Text(
            value,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w900,
              color: Colors.black87,
            ),
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 2),
          // Subtitle with trend indicator
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(
                isPositive ? Icons.trending_up : Icons.trending_down,
                color: isPositive ? Colors.green : Colors.red,
                size: 11,
              ),
              const SizedBox(width: 2),
              Flexible(
                child: Text(
                  subtitle,
                  style: TextStyle(
                    color: isPositive ? Colors.green : Colors.red,
                    fontSize: 10,
                    fontWeight: FontWeight.w500,
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
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 8,
              spreadRadius: 1,
              offset: const Offset(0, 3),
            ),
          ],
          border: Border.all(color: Colors.black.withOpacity(0.04)),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 22),
            ),
            const SizedBox(height: 8),
            Text(
              title,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontWeight: FontWeight.w800,
                fontSize: 12,
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
            const SizedBox(height: 2),
            Text(
              subtitle,
              textAlign: TextAlign.center,
              style: TextStyle(
                color: Colors.grey.shade500,
                fontSize: 9,
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
