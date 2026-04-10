import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../core/api/api_client.dart';

class CustomersScreen extends ConsumerStatefulWidget {
  const CustomersScreen({super.key});

  static const routeName = 'customers';
  static const routePath = '/customers';

  @override
  ConsumerState<CustomersScreen> createState() => _CustomersScreenState();
}

class _CustomersScreenState extends ConsumerState<CustomersScreen> {
  final Color primaryGreen = const Color(0xFF2E7D32);
  final _searchController = TextEditingController();
  
  List<Map<String, dynamic>> _customers = [];
  List<Map<String, dynamic>> _customerGroups = [];
  bool _isLoading = true;
  String _selectedFilter = 'all';
  
  // Stats
  Map<String, dynamic> _stats = {
    'total_customers': 0,
    'active_customers': 0,
    'credit_customers': 0,
    'total_outstanding': 0,
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
      _fetchCustomers(),
      _fetchCustomerGroups(),
      _fetchStats(),
    ]);
  }

  Future<void> _fetchCustomers() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/customers', queryParameters: {
        'q': _searchController.text,
        'filter': _selectedFilter == 'all' ? null : _selectedFilter,
      }).timeout(const Duration(seconds: 10));
      
      if (mounted) {
        setState(() {
          _customers = List<Map<String, dynamic>>.from(res.data['data'] ?? []);
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _customers = [
            {'id': 1, 'name': 'John Doe', 'phone': '0712345678', 'email': 'john@example.com', 'balance': 0, 'total_purchases': 150000},
            {'id': 2, 'name': 'Jane Smith', 'phone': '0723456789', 'email': 'jane@example.com', 'balance': 45000, 'total_purchases': 320000},
            {'id': 3, 'name': 'Bob Johnson', 'phone': '0734567890', 'email': 'bob@example.com', 'balance': 0, 'total_purchases': 89000},
            {'id': 4, 'name': 'Alice Brown', 'phone': '0745678901', 'email': 'alice@example.com', 'balance': 120000, 'total_purchases': 500000},
            {'id': 5, 'name': 'Charlie Wilson', 'phone': '0756789012', 'email': 'charlie@example.com', 'balance': 0, 'total_purchases': 0},
          ];
          _isLoading = false;
        });
      }
    }
  }

  Future<void> _fetchCustomerGroups() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/customer-groups').timeout(const Duration(seconds: 10));
      if (mounted) {
        setState(() {
          _customerGroups = List<Map<String, dynamic>>.from(res.data['data'] ?? []);
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _customerGroups = [
            {'id': 'all', 'name': 'All Customers'},
            {'id': 'vip', 'name': 'VIP'},
            {'id': 'regular', 'name': 'Regular'},
            {'id': 'credit', 'name': 'Credit'},
          ];
        });
      }
    }
  }

  Future<void> _fetchStats() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/customers/stats').timeout(const Duration(seconds: 10));
      if (mounted) {
        setState(() => _stats = res.data['data'] ?? {});
      }
    } catch (e) {
      // Use fallback stats
    }
  }

  void _showAddCustomerDialog() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (context) => _AddCustomerSheet(
        groups: _customerGroups,
        onSave: (customer) async {
          try {
            final dio = ref.read(apiClientProvider).dio;
            await dio.post('/auth/customers', data: customer);
            Navigator.pop(context);
            _fetchCustomers();
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('Customer added successfully')),
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
    final filteredCustomers = _customers.where((c) {
      final name = c['name'].toString().toLowerCase();
      final phone = c['phone'].toString().toLowerCase();
      final search = _searchController.text.toLowerCase();
      return name.contains(search) || phone.contains(search);
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
          'Customers',
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.add, color: Colors.white),
            onPressed: _showAddCustomerDialog,
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
                  title: 'Total',
                  value: '${_stats['total_customers'] ?? _customers.length}',
                  icon: Icons.people,
                  color: Colors.blue,
                ),
                const SizedBox(width: 12),
                _StatCard(
                  title: 'Active',
                  value: '${_stats['active_customers'] ?? _countActive()}',
                  icon: Icons.verified_user,
                  color: Colors.green,
                ),
                const SizedBox(width: 12),
                _StatCard(
                  title: 'Credit',
                  value: '${_stats['credit_customers'] ?? _countCredit()}',
                  icon: Icons.account_balance_wallet,
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
                    hintText: 'Search customers...',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: _searchController.text.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear),
                            onPressed: () {
                              _searchController.clear();
                              _fetchCustomers();
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
                // Filter Chips
                SizedBox(
                  height: 40,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: _customerGroups.length,
                    itemBuilder: (context, index) {
                      final group = _customerGroups[index];
                      final id = group['id'].toString();
                      final isSelected = _selectedFilter == id;
                      return Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: FilterChip(
                          selected: isSelected,
                          label: Text(group['name'] ?? 'Unknown'),
                          onSelected: (selected) {
                            setState(() => _selectedFilter = selected ? id : 'all');
                            _fetchCustomers();
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
                    icon: Icons.person_add,
                    label: 'Add Customer',
                    color: primaryGreen,
                    onTap: _showAddCustomerDialog,
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _QuickActionButton(
                    icon: Icons.message,
                    label: 'Send SMS',
                    color: Colors.blue,
                    onTap: () {},
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _QuickActionButton(
                    icon: Icons.email,
                    label: 'Email All',
                    color: Colors.orange,
                    onTap: () {},
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _QuickActionButton(
                    icon: Icons.file_download,
                    label: 'Export',
                    color: Colors.purple,
                    onTap: () {},
                  ),
                ),
              ],
            ),
          ),
          
          // Customers List
          Expanded(
            child: Skeletonizer(
              enabled: _isLoading,
              child: ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: filteredCustomers.length,
                itemBuilder: (context, index) {
                  final customer = filteredCustomers[index];
                  return _CustomerListTile(
                    customer: customer,
                    onTap: () => _showCustomerDetails(customer),
                  );
                },
              ),
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _showAddCustomerDialog,
        backgroundColor: primaryGreen,
        icon: const Icon(Icons.person_add),
        label: const Text('Add Customer'),
      ),
    );
  }

  void _showCustomerDetails(Map<String, dynamic> customer) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (context) => _CustomerDetailSheet(
        customer: customer,
        onEdit: () {
          Navigator.pop(context);
          // Show edit dialog
        },
        onDelete: () async {
          try {
            final dio = ref.read(apiClientProvider).dio;
            await dio.delete('/auth/customers/${customer['id']}');
            Navigator.pop(context);
            _fetchCustomers();
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('Customer deleted')),
            );
          } catch (e) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Error: $e')),
            );
          }
        },
        onCall: () {
          // Launch dialer
        },
        onMessage: () {
          // Launch SMS
        },
      ),
    );
  }

  int _countActive() {
    return _customers.where((c) => (c['total_purchases'] ?? 0) > 0).length;
  }

  int _countCredit() {
    return _customers.where((c) => (c['balance'] ?? 0) > 0).length;
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

class _CustomerListTile extends StatelessWidget {
  final Map<String, dynamic> customer;
  final VoidCallback onTap;

  const _CustomerListTile({required this.customer, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final hasCredit = (customer['balance'] ?? 0) > 0;
    final initials = (customer['name'] ?? 'U').toString().split(' ').map((n) => n[0]).take(2).join('').toUpperCase();

    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: ListTile(
        onTap: onTap,
        leading: CircleAvatar(
          backgroundColor: hasCredit ? Colors.orange.withOpacity(0.2) : Colors.blue.withOpacity(0.2),
          child: Text(
            initials,
            style: TextStyle(
              color: hasCredit ? Colors.orange : Colors.blue,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
        title: Text(
          customer['name'] ?? 'Unknown',
          style: const TextStyle(fontWeight: FontWeight.w600),
        ),
        subtitle: Text(customer['phone'] ?? 'No phone'),
        trailing: hasCredit
            ? Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.orange.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  'TZS ${customer['balance']}',
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: Colors.orange,
                  ),
                ),
              )
            : const Icon(Icons.chevron_right, color: Colors.grey),
      ),
    );
  }
}

class _AddCustomerSheet extends StatefulWidget {
  final List<Map<String, dynamic>> groups;
  final Function(Map<String, dynamic>) onSave;

  const _AddCustomerSheet({required this.groups, required this.onSave});

  @override
  State<_AddCustomerSheet> createState() => _AddCustomerSheetState();
}

class _AddCustomerSheetState extends State<_AddCustomerSheet> {
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _emailController = TextEditingController();
  final _addressController = TextEditingController();
  String _selectedGroup = '';

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
                  'Add New Customer',
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
                labelText: 'Full Name',
                prefixIcon: Icon(Icons.person),
              ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _phoneController,
              keyboardType: TextInputType.phone,
              decoration: const InputDecoration(
                labelText: 'Phone Number',
                prefixIcon: Icon(Icons.phone),
              ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _emailController,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(
                labelText: 'Email (Optional)',
                prefixIcon: Icon(Icons.email),
              ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _addressController,
              decoration: const InputDecoration(
                labelText: 'Address (Optional)',
                prefixIcon: Icon(Icons.location_on),
              ),
            ),
            const SizedBox(height: 12),
            if (widget.groups.isNotEmpty)
              DropdownButtonFormField<String>(
                value: _selectedGroup.isEmpty ? null : _selectedGroup,
                hint: const Text('Select Group'),
                items: widget.groups.map((g) {
                  return DropdownMenuItem(
                    value: g['id'].toString(),
                    child: Text(g['name'] ?? 'Unknown'),
                  );
                }).toList(),
                onChanged: (value) => setState(() => _selectedGroup = value ?? ''),
              ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 50,
              child: FilledButton(
                onPressed: () {
                  widget.onSave({
                    'name': _nameController.text,
                    'phone': _phoneController.text,
                    'email': _emailController.text,
                    'address': _addressController.text,
                    'group_id': _selectedGroup,
                  });
                },
                child: const Text('SAVE CUSTOMER'),
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}

class _CustomerDetailSheet extends StatelessWidget {
  final Map<String, dynamic> customer;
  final VoidCallback onEdit;
  final VoidCallback onDelete;
  final VoidCallback onCall;
  final VoidCallback onMessage;

  const _CustomerDetailSheet({
    required this.customer,
    required this.onEdit,
    required this.onDelete,
    required this.onCall,
    required this.onMessage,
  });

  @override
  Widget build(BuildContext context) {
    final initials = (customer['name'] ?? 'U').toString().split(' ').map((n) => n[0]).take(2).join('').toUpperCase();

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
          CircleAvatar(
            radius: 40,
            backgroundColor: Colors.blue.withOpacity(0.2),
            child: Text(
              initials,
              style: const TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: Colors.blue,
              ),
            ),
          ),
          const SizedBox(height: 16),
          Text(
            customer['name'] ?? 'Unknown',
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 20),
          _DetailRow(label: 'Phone', value: customer['phone'] ?? 'N/A'),
          _DetailRow(label: 'Email', value: customer['email'] ?? 'N/A'),
          _DetailRow(label: 'Address', value: customer['address'] ?? 'N/A'),
          _DetailRow(label: 'Total Purchases', value: 'TZS ${customer['total_purchases'] ?? 0}'),
          _DetailRow(label: 'Outstanding Balance', value: 'TZS ${customer['balance'] ?? 0}'),
          const SizedBox(height: 20),
          Row(
            children: [
              Expanded(
                child: FilledButton.icon(
                  onPressed: onCall,
                  icon: const Icon(Icons.phone),
                  label: const Text('CALL'),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: FilledButton.icon(
                  onPressed: onMessage,
                  icon: const Icon(Icons.message),
                  label: const Text('SMS'),
                  style: FilledButton.styleFrom(backgroundColor: Colors.teal),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: onEdit,
                  icon: const Icon(Icons.edit),
                  label: const Text('EDIT'),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: onDelete,
                  icon: const Icon(Icons.delete, color: Colors.red),
                  label: const Text('DELETE', style: TextStyle(color: Colors.red)),
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: Colors.red),
                  ),
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
