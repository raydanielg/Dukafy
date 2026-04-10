import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../core/api/api_client.dart';
import '../auth/user_provider.dart';

class SaleScreen extends ConsumerStatefulWidget {
  const SaleScreen({super.key});

  static const routeName = 'sale';
  static const routePath = '/sale';

  @override
  ConsumerState<SaleScreen> createState() => _SaleScreenState();
}

class _SaleScreenState extends ConsumerState<SaleScreen> {
  final Color primaryGreen = const Color(0xFF2E7D32);

  // Sale items
  List<Map<String, dynamic>> _saleItems = [];
  List<Map<String, dynamic>> _products = [];
  List<Map<String, dynamic>> _customers = [];

  // Loading states
  bool _isLoading = true;
  bool _isSubmitting = false;
  bool _productsLoading = true;
  bool _customersLoading = true;

  // Form controllers
  final _searchController = TextEditingController();
  Map<String, dynamic>? _selectedCustomer;
  String _paymentMethod = 'cash';
  String? _notes;

  // Calculations
  double get _subtotal => _saleItems.fold(0, (sum, item) => sum + ((item['price'] ?? 0) * (item['quantity'] ?? 1)));
  double get _tax => _subtotal * 0.18; // 18% VAT
  double get _total => _subtotal + _tax;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadData());
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    await Future.wait([
      _fetchProducts(),
      _fetchCustomers(),
    ]);
    setState(() => _isLoading = false);
  }

  Future<void> _fetchProducts() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/products').timeout(const Duration(seconds: 10));
      if (mounted) {
        setState(() {
          _products = List<Map<String, dynamic>>.from(res.data['data'] ?? res.data ?? []);
          _productsLoading = false;
        });
      }
    } catch (e) {
      // Fallback data if API fails
      if (mounted) {
        setState(() {
          _products = [
            {'id': 1, 'name': 'Rice 1kg', 'price': 3500, 'stock': 50},
            {'id': 2, 'name': 'Sugar 1kg', 'price': 2800, 'stock': 30},
            {'id': 3, 'name': 'Cooking Oil 1L', 'price': 8500, 'stock': 20},
            {'id': 4, 'name': 'Soap Bar', 'price': 1500, 'stock': 100},
            {'id': 5, 'name': 'Bread', 'price': 2000, 'stock': 25},
            {'id': 6, 'name': 'Milk 1L', 'price': 3200, 'stock': 40},
          ];
          _productsLoading = false;
        });
      }
    }
  }

  Future<void> _fetchCustomers() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/customers').timeout(const Duration(seconds: 10));
      if (mounted) {
        setState(() {
          _customers = List<Map<String, dynamic>>.from(res.data['data'] ?? res.data ?? []);
          _customersLoading = false;
        });
      }
    } catch (e) {
      // Fallback data if API fails
      if (mounted) {
        setState(() {
          _customers = [
            {'id': 1, 'name': 'Cash Customer', 'phone': null},
            {'id': 2, 'name': 'John Doe', 'phone': '0712345678'},
            {'id': 3, 'name': 'Jane Smith', 'phone': '0723456789'},
          ];
          _customersLoading = false;
        });
      }
    }
  }

  void _addToCart(Map<String, dynamic> product) {
    final existingIndex = _saleItems.indexWhere((item) => item['product_id'] == product['id']);
    if (existingIndex >= 0) {
      setState(() {
        _saleItems[existingIndex]['quantity'] = (_saleItems[existingIndex]['quantity'] ?? 1) + 1;
      });
    } else {
      setState(() {
        _saleItems.add({
          'product_id': product['id'],
          'name': product['name'],
          'price': product['price'] ?? 0,
          'quantity': 1,
        });
      });
    }
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('${product['name']} added to cart')),
    );
  }

  void _updateQuantity(int index, int quantity) {
    if (quantity <= 0) {
      setState(() => _saleItems.removeAt(index));
    } else {
      setState(() => _saleItems[index]['quantity'] = quantity);
    }
  }

  Future<void> _submitSale() async {
    if (_saleItems.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please add items to cart')),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.post('/auth/sales', data: {
        'customer_id': _selectedCustomer?['id'],
        'items': _saleItems.map((item) => ({
          'product_id': item['product_id'],
          'quantity': item['quantity'],
          'price': item['price'],
        })).toList(),
        'payment_method': _paymentMethod,
        'subtotal': _subtotal,
        'tax': _tax,
        'total': _total,
        'notes': _notes,
      }).timeout(const Duration(seconds: 15));

      if (mounted) {
        setState(() {
          _isSubmitting = false;
          _saleItems = [];
          _selectedCustomer = null;
          _paymentMethod = 'cash';
        });

        // Show success dialog
        showDialog(
          context: context,
          builder: (context) => AlertDialog(
            title: const Row(
              children: [
                Icon(Icons.check_circle, color: Colors.green),
                SizedBox(width: 8),
                Text('Sale Complete'),
              ],
            ),
            content: Text('Sale #${res.data['sale_id'] ?? 'N/A'} recorded successfully.\n\nTotal: TZS ${_total.toStringAsFixed(2)}'),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.pop(context);
                  context.pop();
                },
                child: const Text('DONE'),
              ),
              FilledButton(
                onPressed: () {
                  Navigator.pop(context);
                },
                child: const Text('NEW SALE'),
              ),
            ],
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isSubmitting = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        elevation: 0,
        backgroundColor: primaryGreen,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => context.pop(),
        ),
        title: const Text(
          'New Sale',
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.history, color: Colors.white),
            onPressed: () {},
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                // Customer & Payment Section
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.05),
                        blurRadius: 10,
                      ),
                    ],
                  ),
                  child: Column(
                    children: [
                      // Customer Selector
                      Skeletonizer(
                        enabled: _customersLoading,
                        child: GestureDetector(
                          onTap: _showCustomerPicker,
                          child: Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Colors.grey.shade100,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.grey.shade300),
                            ),
                            child: Row(
                              children: [
                                Icon(Icons.person_outline, color: primaryGreen),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Text(
                                        'Customer',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey,
                                        ),
                                      ),
                                      Text(
                                        _selectedCustomer?['name'] ?? 'Select Customer',
                                        style: const TextStyle(
                                          fontWeight: FontWeight.w600,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const Icon(Icons.chevron_right, color: Colors.grey),
                              ],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      // Payment Method
                      Row(
                        children: [
                          Expanded(
                            child: _PaymentMethodChip(
                              icon: Icons.money,
                              label: 'Cash',
                              isSelected: _paymentMethod == 'cash',
                              onTap: () => setState(() => _paymentMethod = 'cash'),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: _PaymentMethodChip(
                              icon: Icons.credit_card,
                              label: 'Card',
                              isSelected: _paymentMethod == 'card',
                              onTap: () => setState(() => _paymentMethod = 'card'),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: _PaymentMethodChip(
                              icon: Icons.account_balance_wallet,
                              label: 'Credit',
                              isSelected: _paymentMethod == 'credit',
                              onTap: () => setState(() => _paymentMethod = 'credit'),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                // Cart Items or Products
                Expanded(
                  child: _saleItems.isEmpty
                      ? _buildProductsGrid()
                      : _buildCartView(),
                ),

                // Bottom Summary Bar
                if (_saleItems.isNotEmpty)
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.1),
                          blurRadius: 10,
                          offset: const Offset(0, -5),
                        ),
                      ],
                    ),
                    child: SafeArea(
                      child: Column(
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                '${_saleItems.length} Items',
                                style: TextStyle(color: Colors.grey.shade600),
                              ),
                              TextButton(
                                onPressed: () => setState(() => _saleItems = []),
                                child: const Text('Clear'),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text('Subtotal'),
                              Text('TZS ${_subtotal.toStringAsFixed(2)}'),
                            ],
                          ),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text('Tax (18%)'),
                              Text('TZS ${_tax.toStringAsFixed(2)}'),
                            ],
                          ),
                          const Divider(),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text(
                                'TOTAL',
                                style: TextStyle(fontWeight: FontWeight.bold),
                              ),
                              Text(
                                'TZS ${_total.toStringAsFixed(2)}',
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 18,
                                  color: primaryGreen,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          SizedBox(
                            width: double.infinity,
                            height: 50,
                            child: FilledButton(
                              onPressed: _isSubmitting ? null : _submitSale,
                              style: FilledButton.styleFrom(
                                backgroundColor: primaryGreen,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12),
                                ),
                              ),
                              child: _isSubmitting
                                  ? const CircularProgressIndicator(color: Colors.white)
                                  : const Text(
                                      'COMPLETE SALE',
                                      style: TextStyle(fontWeight: FontWeight.bold),
                                    ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
              ],
            ),
      floatingActionButton: _saleItems.isNotEmpty
          ? FloatingActionButton(
              backgroundColor: Colors.grey.shade700,
              onPressed: () => setState(() => _saleItems = []),
              child: const Icon(Icons.add_shopping_cart),
            )
          : null,
    );
  }

  Widget _buildProductsGrid() {
    return Skeletonizer(
      enabled: _productsLoading,
      child: Column(
        children: [
          // Search Bar
          Padding(
            padding: const EdgeInsets.all(16),
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Search products...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchController.clear();
                          setState(() {});
                        },
                      )
                    : null,
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide.none,
                ),
              ),
              onChanged: (value) => setState(() {}),
            ),
          ),
          // Products Grid
          Expanded(
            child: GridView.builder(
              padding: const EdgeInsets.all(16),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 3,
                childAspectRatio: 0.8,
                crossAxisSpacing: 10,
                mainAxisSpacing: 10,
              ),
              itemCount: _filteredProducts.length,
              itemBuilder: (context, index) {
                final product = _filteredProducts[index];
                return _ProductCard(
                  product: product,
                  onTap: () => _addToCart(product),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  List<Map<String, dynamic>> get _filteredProducts {
    if (_searchController.text.isEmpty) return _products;
    return _products.where((p) =>
        p['name'].toString().toLowerCase().contains(_searchController.text.toLowerCase())
    ).toList();
  }

  Widget _buildCartView() {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _saleItems.length,
      itemBuilder: (context, index) {
        final item = _saleItems[index];
        final quantity = item['quantity'] ?? 1;
        final price = item['price'] ?? 0;
        final total = price * quantity;

        return Card(
          margin: const EdgeInsets.only(bottom: 8),
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        item['name'],
                        style: const TextStyle(fontWeight: FontWeight.w600),
                      ),
                      Text(
                        'TZS ${price.toStringAsFixed(2)} each',
                        style: TextStyle(
                          color: Colors.grey.shade600,
                          fontSize: 12,
                        ),
                      ),
                    ],
                  ),
                ),
                Row(
                  children: [
                    IconButton(
                      icon: const Icon(Icons.remove_circle_outline),
                      onPressed: () => _updateQuantity(index, quantity - 1),
                    ),
                    Text(
                      '$quantity',
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                    IconButton(
                      icon: const Icon(Icons.add_circle_outline, color: Colors.green),
                      onPressed: () => _updateQuantity(index, quantity + 1),
                    ),
                  ],
                ),
                Text(
                  'TZS ${total.toStringAsFixed(2)}',
                  style: const TextStyle(fontWeight: FontWeight.w600),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  void _showCustomerPicker() {
    showModalBottomSheet(
      context: context,
      builder: (context) => Container(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Select Customer',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('Cancel'),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Expanded(
              child: ListView.builder(
                itemCount: _customers.length,
                itemBuilder: (context, index) {
                  final customer = _customers[index];
                  final isSelected = _selectedCustomer?['id'] == customer['id'];
                  return ListTile(
                    leading: CircleAvatar(
                      backgroundColor: isSelected ? primaryGreen : Colors.grey.shade300,
                      child: Icon(
                        Icons.person,
                        color: isSelected ? Colors.white : Colors.grey,
                      ),
                    ),
                    title: Text(customer['name']),
                    subtitle: customer['phone'] != null ? Text(customer['phone']) : null,
                    trailing: isSelected
                        ? Icon(Icons.check_circle, color: primaryGreen)
                        : null,
                    onTap: () {
                      setState(() => _selectedCustomer = customer);
                      Navigator.pop(context);
                    },
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ProductCard extends StatelessWidget {
  final Map<String, dynamic> product;
  final VoidCallback onTap;

  const _ProductCard({required this.product, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 8,
            ),
          ],
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFF2E7D32).withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.shopping_bag_outlined,
                color: Color(0xFF2E7D32),
                size: 28,
              ),
            ),
            const SizedBox(height: 8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 8),
              child: Text(
                product['name'] ?? 'Product',
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'TZS ${product['price'] ?? 0}',
              style: const TextStyle(
                fontSize: 11,
                color: Colors.grey,
              ),
            ),
            const SizedBox(height: 4),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                color: const Color(0xFF2E7D32),
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Text(
                'ADD',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _PaymentMethodChip extends StatelessWidget {
  final IconData icon;
  final String label;
  final bool isSelected;
  final VoidCallback onTap;

  const _PaymentMethodChip({
    required this.icon,
    required this.label,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFF2E7D32) : Colors.grey.shade200,
          borderRadius: BorderRadius.circular(10),
        ),
        child: Column(
          children: [
            Icon(
              icon,
              color: isSelected ? Colors.white : Colors.grey.shade600,
              size: 20,
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: TextStyle(
                color: isSelected ? Colors.white : Colors.grey.shade600,
                fontSize: 11,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
