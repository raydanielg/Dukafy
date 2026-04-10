import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../core/api/api_client.dart';
import '../sales/sale_screen.dart';

class ProductsScreen extends ConsumerStatefulWidget {
  const ProductsScreen({super.key});

  static const routeName = 'products';
  static const routePath = '/products';

  @override
  ConsumerState<ProductsScreen> createState() => _ProductsScreenState();
}

class _ProductsScreenState extends ConsumerState<ProductsScreen> {
  final Color primaryGreen = const Color(0xFF2E7D32);
  final _searchController = TextEditingController();
  
  List<Map<String, dynamic>> _products = [];
  List<Map<String, dynamic>> _categories = [];
  bool _isLoading = true;
  bool _isGridView = true;
  String _selectedCategory = 'all';
  
  // Stats
  Map<String, dynamic> _stats = {
    'total_products': 0,
    'total_value': 0,
    'low_stock': 0,
    'out_of_stock': 0,
  };

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
      _fetchCategories(),
      _fetchStats(),
    ]);
  }

  Future<void> _fetchProducts() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/products', queryParameters: {
        'q': _searchController.text,
        'category': _selectedCategory == 'all' ? null : _selectedCategory,
      }).timeout(const Duration(seconds: 10));
      
      if (mounted) {
        setState(() {
          _products = List<Map<String, dynamic>>.from(res.data['data'] ?? []);
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _products = [
            {'id': 1, 'name': 'Rice 1kg', 'price': 3500, 'stock_qty': 50, 'category': 'Food'},
            {'id': 2, 'name': 'Sugar 1kg', 'price': 2800, 'stock_qty': 30, 'category': 'Food'},
            {'id': 3, 'name': 'Cooking Oil 1L', 'price': 8500, 'stock_qty': 20, 'category': 'Food'},
            {'id': 4, 'name': 'Soap Bar', 'price': 1500, 'stock_qty': 5, 'category': 'Household'},
            {'id': 5, 'name': 'Bread', 'price': 2000, 'stock_qty': 0, 'category': 'Food'},
            {'id': 6, 'name': 'Milk 1L', 'price': 3200, 'stock_qty': 40, 'category': 'Food'},
          ];
          _isLoading = false;
        });
      }
    }
  }

  Future<void> _fetchCategories() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/product-categories').timeout(const Duration(seconds: 10));
      if (mounted) {
        setState(() {
          _categories = List<Map<String, dynamic>>.from(res.data['data'] ?? []);
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _categories = [
            {'id': 'all', 'name': 'All'},
            {'id': 'food', 'name': 'Food'},
            {'id': 'household', 'name': 'Household'},
            {'id': 'electronics', 'name': 'Electronics'},
          ];
        });
      }
    }
  }

  Future<void> _fetchStats() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/products/stats').timeout(const Duration(seconds: 10));
      if (mounted) {
        setState(() => _stats = res.data['data'] ?? {});
      }
    } catch (e) {
      // Use fallback stats
    }
  }

  void _showAddProductDialog() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (context) => _AddProductSheet(
        categories: _categories,
        onSave: (product) async {
          try {
            final dio = ref.read(apiClientProvider).dio;
            await dio.post('/products', data: product);
            Navigator.pop(context);
            _fetchProducts();
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('Product added successfully')),
            );
          } catch (e) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Error: $e')),
            );
          }
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final filteredProducts = _products.where((p) {
      final name = p['name'].toString().toLowerCase();
      final search = _searchController.text.toLowerCase();
      return name.contains(search);
    }).toList();

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
          'Products & Stock',
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700),
        ),
        actions: [
          IconButton(
            icon: Icon(_isGridView ? Icons.list : Icons.grid_view, color: Colors.white),
            onPressed: () => setState(() => _isGridView = !_isGridView),
          ),
          IconButton(
            icon: const Icon(Icons.add, color: Colors.white),
            onPressed: _showAddProductDialog,
          ),
        ],
      ),
      body: Column(
        children: [
          // Stats Cards
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.white,
            child: Row(
              children: [
                _StatCard(
                  title: 'Products',
                  value: '${_stats['total_products'] ?? _products.length}',
                  icon: Icons.inventory_2,
                  color: Colors.blue,
                ),
                const SizedBox(width: 12),
                _StatCard(
                  title: 'Stock Value',
                  value: 'TZS ${_formatNumber(_stats['total_value'] ?? _calculateTotalValue())}',
                  icon: Icons.attach_money,
                  color: Colors.green,
                ),
                const SizedBox(width: 12),
                _StatCard(
                  title: 'Low Stock',
                  value: '${_stats['low_stock'] ?? _countLowStock()}',
                  icon: Icons.warning,
                  color: Colors.orange,
                ),
              ],
            ),
          ),
          
          // Search & Filter
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.white,
            child: Column(
              children: [
                TextField(
                  controller: _searchController,
                  decoration: InputDecoration(
                    hintText: 'Search products...',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: _searchController.text.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear),
                            onPressed: () {
                              _searchController.clear();
                              _fetchProducts();
                            },
                          )
                        : null,
                    filled: true,
                    fillColor: Colors.grey.shade100,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide.none,
                    ),
                  ),
                  onChanged: (value) => setState(() {}),
                ),
                const SizedBox(height: 12),
                // Category Chips
                SizedBox(
                  height: 40,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _categories.length + 1,
                    itemBuilder: (context, index) {
                      if (index == 0) {
                        final isSelected = _selectedCategory == 'all';
                        return Padding(
                          padding: const EdgeInsets.only(right: 8),
                          child: FilterChip(
                            selected: isSelected,
                            label: const Text('All'),
                            onSelected: (selected) {
                              setState(() => _selectedCategory = 'all');
                              _fetchProducts();
                            },
                            selectedColor: primaryGreen.withOpacity(0.2),
                            checkmarkColor: primaryGreen,
                          ),
                        );
                      }
                      final category = _categories[index - 1];
                      final id = category['id'].toString();
                      final isSelected = _selectedCategory == id;
                      return Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: FilterChip(
                          selected: isSelected,
                          label: Text(category['name'] ?? 'Unknown'),
                          onSelected: (selected) {
                            setState(() => _selectedCategory = selected ? id : 'all');
                            _fetchProducts();
                          },
                          selectedColor: primaryGreen.withOpacity(0.2),
                          checkmarkColor: primaryGreen,
                        ),
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
          
          // Quick Actions
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            color: Colors.white,
            child: Row(
              children: [
                Expanded(
                  child: _QuickActionButton(
                    icon: Icons.add,
                    label: 'Add Product',
                    color: primaryGreen,
                    onTap: _showAddProductDialog,
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _QuickActionButton(
                    icon: Icons.qr_code_scanner,
                    label: 'Scan Barcode',
                    color: Colors.blue,
                    onTap: () {},
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _QuickActionButton(
                    icon: Icons.file_download,
                    label: 'Export',
                    color: Colors.orange,
                    onTap: () {},
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _QuickActionButton(
                    icon: Icons.point_of_sale,
                    label: 'Quick Sale',
                    color: Colors.purple,
                    onTap: () => context.push(SaleScreen.routePath),
                  ),
                ),
              ],
            ),
          ),
          
          // Products List
          Expanded(
            child: Skeletonizer(
              enabled: _isLoading,
              child: _isGridView
                  ? _buildGridView(filteredProducts)
                  : _buildListView(filteredProducts),
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _showAddProductDialog,
        backgroundColor: primaryGreen,
        icon: const Icon(Icons.add),
        label: const Text('Add Product'),
      ),
    );
  }

  Widget _buildGridView(List<Map<String, dynamic>> products) {
    if (products.isEmpty) {
      return _buildEmptyState();
    }
    return GridView.builder(
      padding: const EdgeInsets.all(16),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        childAspectRatio: 0.75,
        crossAxisSpacing: 12,
        mainAxisSpacing: 12,
      ),
      itemCount: products.length,
      itemBuilder: (context, index) {
        final product = products[index];
        return _ProductCard(
          product: product,
          onTap: () => _showProductDetails(product),
        );
      },
    );
  }

  Widget _buildListView(List<Map<String, dynamic>> products) {
    if (products.isEmpty) {
      return _buildEmptyState();
    }
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: products.length,
      itemBuilder: (context, index) {
        final product = products[index];
        return _ProductListTile(
          product: product,
          onTap: () => _showProductDetails(product),
        );
      },
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Illustration
            Image.asset(
              'assets/images/Sandy_Bus-26_Single-01.jpg',
              height: 200,
              fit: BoxFit.contain,
            ),
            const SizedBox(height: 24),
            // Title
            const Text(
              'No Products Yet',
              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: Colors.black87,
              ),
            ),
            const SizedBox(height: 12),
            // Description
            Text(
              'Get started by adding your first product to the inventory.',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey.shade600,
              ),
            ),
            const SizedBox(height: 32),
            // CTA Button
            FilledButton.icon(
              onPressed: _showAddProductDialog,
              icon: const Icon(Icons.add),
              label: const Text('Add Your First Product'),
              style: FilledButton.styleFrom(
                backgroundColor: primaryGreen,
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showProductDetails(Map<String, dynamic> product) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (context) => _ProductDetailSheet(
        product: product,
        onEdit: () {
          Navigator.pop(context);
          // Show edit dialog
        },
        onDelete: () async {
          try {
            final dio = ref.read(apiClientProvider).dio;
            await dio.delete('/products/${product['id']}');
            Navigator.pop(context);
            _fetchProducts();
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('Product deleted')),
            );
          } catch (e) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Error: $e')),
            );
          }
        },
      ),
    );
  }

  String _formatNumber(dynamic value) {
    if (value == null) return '0';
    final num = value is int ? value : (value is double ? value.toInt() : 0);
    return num.toString().replaceAllMapped(RegExp(r'\B(?=(\d{3})+(?!\d))'), (match) => ',');
  }

  int _calculateTotalValue() {
    return _products.fold(0, (sum, p) => sum + ((p['price'] ?? 0) * (p['stock_qty'] ?? 0)) as int);
  }

  int _countLowStock() {
    return _products.where((p) => (p['stock_qty'] ?? 0) < 10 && (p['stock_qty'] ?? 0) > 0).length;
  }
}

class _StatCard extends StatelessWidget {
  final String title;
  final String value;
  final IconData icon;
  final Color color;

  const _StatCard({
    required this.title,
    required this.value,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 20),
            const SizedBox(height: 4),
            Text(
              value,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
            Text(
              title,
              style: TextStyle(
                fontSize: 10,
                color: Colors.grey.shade600,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _QuickActionButton extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;

  const _QuickActionButton({
    required this.icon,
    required this.label,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: color.withOpacity(0.2)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, color: color, size: 20),
            const SizedBox(height: 4),
            Text(
              label,
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.w600,
                color: color,
              ),
              textAlign: TextAlign.center,
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
    final stockQty = product['stock_qty'] ?? 0;
    final isLowStock = stockQty < 10 && stockQty > 0;
    final isOutOfStock = stockQty == 0;

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
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Product Image Placeholder
            Container(
              height: 100,
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
              ),
              child: Center(
                child: Icon(
                  Icons.inventory_2,
                  size: 40,
                  color: Colors.grey.shade400,
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    product['name'] ?? 'Unknown',
                    style: const TextStyle(
                      fontWeight: FontWeight.w700,
                      fontSize: 14,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'TZS ${product['price'] ?? 0}',
                    style: TextStyle(
                      color: Colors.grey.shade600,
                      fontSize: 12,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: isOutOfStock
                              ? Colors.red.withOpacity(0.1)
                              : isLowStock
                                  ? Colors.orange.withOpacity(0.1)
                                  : Colors.green.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          '$stockQty in stock',
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.w600,
                            color: isOutOfStock
                                ? Colors.red
                                : isLowStock
                                    ? Colors.orange
                                    : Colors.green,
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
    );
  }
}

class _ProductListTile extends StatelessWidget {
  final Map<String, dynamic> product;
  final VoidCallback onTap;

  const _ProductListTile({required this.product, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final stockQty = product['stock_qty'] ?? 0;
    final isLowStock = stockQty < 10 && stockQty > 0;
    final isOutOfStock = stockQty == 0;

    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: ListTile(
        onTap: onTap,
        leading: Container(
          width: 50,
          height: 50,
          decoration: BoxDecoration(
            color: Colors.grey.shade100,
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(Icons.inventory_2, color: Colors.grey.shade400),
        ),
        title: Text(
          product['name'] ?? 'Unknown',
          style: const TextStyle(fontWeight: FontWeight.w600),
        ),
        subtitle: Text('TZS ${product['price'] ?? 0}'),
        trailing: Container(
          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
          decoration: BoxDecoration(
            color: isOutOfStock
                ? Colors.red.withOpacity(0.1)
                : isLowStock
                    ? Colors.orange.withOpacity(0.1)
                    : Colors.green.withOpacity(0.1),
            borderRadius: BorderRadius.circular(6),
          ),
          child: Text(
            '$stockQty',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: isOutOfStock
                  ? Colors.red
                  : isLowStock
                      ? Colors.orange
                      : Colors.green,
            ),
          ),
        ),
      ),
    );
  }
}

class _AddProductSheet extends StatefulWidget {
  final List<Map<String, dynamic>> categories;
  final Function(Map<String, dynamic>) onSave;

  const _AddProductSheet({required this.categories, required this.onSave});

  @override
  State<_AddProductSheet> createState() => _AddProductSheetState();
}

class _AddProductSheetState extends State<_AddProductSheet> {
  final _nameController = TextEditingController();
  final _priceController = TextEditingController();
  final _costController = TextEditingController();
  final _stockController = TextEditingController();
  final _skuController = TextEditingController();
  String _selectedCategory = '';

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
      ),
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Add New Product',
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.close),
                  onPressed: () => Navigator.pop(context),
                ),
              ],
            ),
            const SizedBox(height: 20),
            TextField(
              controller: _nameController,
              decoration: const InputDecoration(
                labelText: 'Product Name',
                prefixIcon: Icon(Icons.inventory_2),
              ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _skuController,
              decoration: const InputDecoration(
                labelText: 'SKU (Optional)',
                prefixIcon: Icon(Icons.qr_code),
              ),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _priceController,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(
                      labelText: 'Price',
                      prefixText: 'TZS ',
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: TextField(
                    controller: _costController,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(
                      labelText: 'Cost',
                      prefixText: 'TZS ',
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _stockController,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(
                labelText: 'Stock Quantity',
                prefixIcon: Icon(Icons.inventory),
              ),
            ),
            const SizedBox(height: 12),
            if (widget.categories.isNotEmpty)
              DropdownButtonFormField<String>(
                value: _selectedCategory.isEmpty ? null : _selectedCategory,
                hint: const Text('Select Category'),
                items: widget.categories.map((c) {
                  return DropdownMenuItem(
                    value: c['id'].toString(),
                    child: Text(c['name'] ?? 'Unknown'),
                  );
                }).toList(),
                onChanged: (value) => setState(() => _selectedCategory = value ?? ''),
              ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 50,
              child: FilledButton(
                onPressed: () {
                  widget.onSave({
                    'name': _nameController.text,
                    'sku': _skuController.text,
                    'price': double.tryParse(_priceController.text) ?? 0,
                    'cost': double.tryParse(_costController.text) ?? 0,
                    'stock_qty': int.tryParse(_stockController.text) ?? 0,
                    'category_id': _selectedCategory,
                  });
                },
                child: const Text('SAVE PRODUCT'),
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}

class _ProductDetailSheet extends StatelessWidget {
  final Map<String, dynamic> product;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  const _ProductDetailSheet({
    required this.product,
    required this.onEdit,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 40,
            height: 4,
            decoration: BoxDecoration(
              color: Colors.grey.shade300,
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          const SizedBox(height: 20),
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: Colors.grey.shade100,
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.inventory_2, size: 40, color: Colors.grey.shade400),
          ),
          const SizedBox(height: 16),
          Text(
            product['name'] ?? 'Unknown',
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 20),
          _DetailRow(label: 'Price', value: 'TZS ${product['price'] ?? 0}'),
          _DetailRow(label: 'Cost', value: 'TZS ${product['cost'] ?? 0}'),
          _DetailRow(label: 'Stock', value: '${product['stock_qty'] ?? 0} units'),
          _DetailRow(label: 'SKU', value: product['sku'] ?? 'N/A'),
          _DetailRow(label: 'Category', value: product['category'] ?? 'N/A'),
          const SizedBox(height: 20),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: onEdit,
                  icon: const Icon(Icons.edit),
                  label: const Text('EDIT'),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: FilledButton.icon(
                  onPressed: onDelete,
                  icon: const Icon(Icons.delete),
                  label: const Text('DELETE'),
                  style: FilledButton.styleFrom(backgroundColor: Colors.red),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _DetailRow extends StatelessWidget {
  final String label;
  final String value;

  const _DetailRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              color: Colors.grey.shade600,
              fontSize: 14,
            ),
          ),
          Text(
            value,
            style: const TextStyle(
              fontWeight: FontWeight.w600,
              fontSize: 14,
            ),
          ),
        ],
      ),
    );
  }
}
