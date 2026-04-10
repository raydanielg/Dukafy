import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../core/api/api_client.dart';

class PaymentScreen extends ConsumerStatefulWidget {
  const PaymentScreen({super.key});

  static const routeName = 'payment';
  static const routePath = '/payment';

  @override
  ConsumerState<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends ConsumerState<PaymentScreen> {
  final Color primaryGreen = const Color(0xFF2E7D32);

  // Payment types
  final List<Map<String, dynamic>> _paymentTypes = [
    {'id': 'expense', 'name': 'Expense', 'icon': Icons.receipt_long, 'color': Colors.red},
    {'id': 'supplier', 'name': 'Supplier', 'icon': Icons.local_shipping, 'color': Colors.orange},
    {'id': 'salary', 'name': 'Salary', 'icon': Icons.people, 'color': Colors.blue},
    {'id': 'debt', 'name': 'Debt Payment', 'icon': Icons.account_balance, 'color': Colors.purple},
    {'id': 'utility', 'name': 'Utility', 'icon': Icons.electrical_services, 'color': Colors.amber},
    {'id': 'rent', 'name': 'Rent', 'icon': Icons.home, 'color': Colors.teal},
  ];

  // Data
  List<Map<String, dynamic>> _recentPayments = [];
  List<Map<String, dynamic>> _suppliers = [];
  List<Map<String, dynamic>> _employees = [];

  // Loading states
  bool _isLoading = true;
  bool _isSubmitting = false;
  bool _recentLoading = true;

  // Selected payment type
  String? _selectedPaymentType;
  Map<String, dynamic>? _selectedRecipient;

  // Form controllers
  final _amountController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _referenceController = TextEditingController();

  // Payment method
  String _paymentMethod = 'cash';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadData());
  }

  @override
  void dispose() {
    _amountController.dispose();
    _descriptionController.dispose();
    _referenceController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    await Future.wait([
      _fetchRecentPayments(),
      _fetchSuppliers(),
      _fetchEmployees(),
    ]);
    setState(() => _isLoading = false);
  }

  Future<void> _fetchRecentPayments() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/payments/recent').timeout(const Duration(seconds: 10));
      if (mounted) {
        setState(() {
          _recentPayments = List<Map<String, dynamic>>.from(res.data['data'] ?? res.data ?? []);
          _recentLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _recentPayments = [
            {'id': 1, 'type': 'expense', 'amount': 50000, 'description': 'Office Supplies', 'date': '2024-01-15'},
            {'id': 2, 'type': 'salary', 'amount': 450000, 'description': 'January Salary', 'date': '2024-01-10'},
            {'id': 3, 'type': 'utility', 'amount': 120000, 'description': 'Electricity Bill', 'date': '2024-01-05'},
          ];
          _recentLoading = false;
        });
      }
    }
  }

  Future<void> _fetchSuppliers() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/suppliers').timeout(const Duration(seconds: 10));
      if (mounted) {
        setState(() => _suppliers = List<Map<String, dynamic>>.from(res.data['data'] ?? res.data ?? []));
      }
    } catch (e) {
      if (mounted) {
        setState(() => _suppliers = [
          {'id': 1, 'name': 'Supplier A', 'phone': '0712345678'},
          {'id': 2, 'name': 'Supplier B', 'phone': '0723456789'},
        ]);
      }
    }
  }

  Future<void> _fetchEmployees() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/employees').timeout(const Duration(seconds: 10));
      if (mounted) {
        setState(() => _employees = List<Map<String, dynamic>>.from(res.data['data'] ?? res.data ?? []));
      }
    } catch (e) {
      if (mounted) {
        setState(() => _employees = [
          {'id': 1, 'name': 'John Doe', 'position': 'Manager'},
          {'id': 2, 'name': 'Jane Smith', 'position': 'Cashier'},
        ]);
      }
    }
  }

  Future<void> _submitPayment() async {
    if (_selectedPaymentType == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select payment type')),
      );
      return;
    }

    if (_amountController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please enter amount')),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.post('/payments', data: {
        'payment_type': _selectedPaymentType,
        'recipient_id': _selectedRecipient?['id'],
        'amount': double.parse(_amountController.text),
        'description': _descriptionController.text,
        'reference': _referenceController.text,
        'payment_method': _paymentMethod,
        'date': DateTime.now().toIso8601String(),
      }).timeout(const Duration(seconds: 15));

      if (mounted) {
        setState(() {
          _isSubmitting = false;
          _amountController.clear();
          _descriptionController.clear();
          _referenceController.clear();
          _selectedPaymentType = null;
          _selectedRecipient = null;
        });

        // Refresh recent payments
        _fetchRecentPayments();

        showDialog(
          context: context,
          builder: (context) => AlertDialog(
            title: const Row(
              children: [
                Icon(Icons.check_circle, color: Colors.green),
                SizedBox(width: 8),
                Text('Payment Recorded'),
              ],
            ),
            content: Text('Payment #${res.data['payment_id'] ?? 'N/A'} has been recorded successfully.'),
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
                child: const Text('NEW PAYMENT'),
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

  void _showRecipientPicker() {
    List<Map<String, dynamic>> recipients = [];
    String title = '';

    switch (_selectedPaymentType) {
      case 'supplier':
        recipients = _suppliers;
        title = 'Select Supplier';
        break;
      case 'salary':
        recipients = _employees;
        title = 'Select Employee';
        break;
      default:
        return;
    }

    showModalBottomSheet(
      context: context,
      builder: (context) => Container(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  title,
                  style: const TextStyle(
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
                itemCount: recipients.length,
                itemBuilder: (context, index) {
                  final recipient = recipients[index];
                  final isSelected = _selectedRecipient?['id'] == recipient['id'];
                  return ListTile(
                    leading: CircleAvatar(
                      backgroundColor: isSelected ? primaryGreen : Colors.grey.shade300,
                      child: Icon(
                        _selectedPaymentType == 'salary' ? Icons.person : Icons.local_shipping,
                        color: isSelected ? Colors.white : Colors.grey,
                      ),
                    ),
                    title: Text(recipient['name']),
                    subtitle: Text(recipient['phone'] ?? recipient['position'] ?? ''),
                    trailing: isSelected
                        ? Icon(Icons.check_circle, color: primaryGreen)
                        : null,
                    onTap: () {
                      setState(() => _selectedRecipient = recipient);
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
          'Record Payment',
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Payment Type Selection
                  const Text(
                    'Payment Type',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 12),
                  GridView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 3,
                      childAspectRatio: 1.1,
                      crossAxisSpacing: 10,
                      mainAxisSpacing: 10,
                    ),
                    itemCount: _paymentTypes.length,
                    itemBuilder: (context, index) {
                      final type = _paymentTypes[index];
                      final isSelected = _selectedPaymentType == type['id'];
                      return GestureDetector(
                        onTap: () {
                          setState(() {
                            _selectedPaymentType = type['id'];
                            _selectedRecipient = null;
                          });
                        },
                        child: Container(
                          decoration: BoxDecoration(
                            color: isSelected ? (type['color'] as Color).withOpacity(0.1) : Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: isSelected ? type['color'] as Color : Colors.grey.shade300,
                              width: isSelected ? 2 : 1,
                            ),
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
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: (type['color'] as Color).withOpacity(0.1),
                                  shape: BoxShape.circle,
                                ),
                                child: Icon(
                                  type['icon'] as IconData,
                                  color: type['color'] as Color,
                                  size: 24,
                                ),
                              ),
                              const SizedBox(height: 6),
                              Text(
                                type['name'] as String,
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w600,
                                  color: isSelected ? type['color'] as Color : Colors.black87,
                                ),
                                textAlign: TextAlign.center,
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                  const SizedBox(height: 24),

                  // Recipient Selection (if applicable)
                  if (_selectedPaymentType == 'supplier' || _selectedPaymentType == 'salary')
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Recipient',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 12),
                        GestureDetector(
                          onTap: _showRecipientPicker,
                          child: Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.grey.shade300),
                            ),
                            child: Row(
                              children: [
                                Icon(
                                  _selectedPaymentType == 'salary' ? Icons.person : Icons.local_shipping,
                                  color: primaryGreen,
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Text(
                                        'Select Recipient',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey,
                                        ),
                                      ),
                                      Text(
                                        _selectedRecipient?['name'] ?? 'Tap to select',
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
                        const SizedBox(height: 24),
                      ],
                    ),

                  // Amount Input
                  const Text(
                    'Amount',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _amountController,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(
                      prefixText: 'TZS ',
                      prefixStyle: TextStyle(
                        color: primaryGreen,
                        fontWeight: FontWeight.bold,
                      ),
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: primaryGreen, width: 2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Description Input
                  const Text(
                    'Description',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _descriptionController,
                    maxLines: 2,
                    decoration: InputDecoration(
                      hintText: 'Enter payment description...',
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: primaryGreen, width: 2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Reference Number
                  const Text(
                    'Reference (Optional)',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _referenceController,
                    decoration: InputDecoration(
                      hintText: 'Invoice #, Receipt #, etc.',
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: primaryGreen, width: 2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Payment Method
                  const Text(
                    'Payment Method',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Expanded(
                        child: _PaymentMethodButton(
                          icon: Icons.money,
                          label: 'Cash',
                          isSelected: _paymentMethod == 'cash',
                          onTap: () => setState(() => _paymentMethod = 'cash'),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: _PaymentMethodButton(
                          icon: Icons.credit_card,
                          label: 'Bank',
                          isSelected: _paymentMethod == 'bank',
                          onTap: () => setState(() => _paymentMethod = 'bank'),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: _PaymentMethodButton(
                          icon: Icons.phone_android,
                          label: 'Mobile',
                          isSelected: _paymentMethod == 'mobile',
                          onTap: () => setState(() => _paymentMethod = 'mobile'),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 32),

                  // Recent Payments
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Recent Payments',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      TextButton(
                        onPressed: () {},
                        child: const Text('See All'),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Skeletonizer(
                    enabled: _recentLoading,
                    child: ListView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: _recentPayments.take(3).length,
                      itemBuilder: (context, index) {
                        final payment = _recentPayments[index];
                        final type = _paymentTypes.firstWhere(
                          (t) => t['id'] == payment['type'],
                          orElse: () => _paymentTypes[0],
                        );
                        return Card(
                          margin: const EdgeInsets.only(bottom: 8),
                          child: ListTile(
                            leading: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: (type['color'] as Color).withOpacity(0.1),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Icon(
                                type['icon'] as IconData,
                                color: type['color'] as Color,
                                size: 20,
                              ),
                            ),
                            title: Text(payment['description'] ?? 'Payment'),
                            subtitle: Text(payment['date'] ?? ''),
                            trailing: Text(
                              'TZS ${payment['amount']}',
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                color: Colors.red,
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                  const SizedBox(height: 80),
                ],
              ),
            ),
      bottomNavigationBar: Container(
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
          child: SizedBox(
            width: double.infinity,
            height: 50,
            child: FilledButton(
              onPressed: _isSubmitting ? null : _submitPayment,
              style: FilledButton.styleFrom(
                backgroundColor: Colors.red,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              child: _isSubmitting
                  ? const CircularProgressIndicator(color: Colors.white)
                  : const Text(
                      'RECORD PAYMENT',
                      style: TextStyle(fontWeight: FontWeight.bold),
                    ),
            ),
          ),
        ),
      ),
    );
  }
}

class _PaymentMethodButton extends StatelessWidget {
  final IconData icon;
  final String label;
  final bool isSelected;
  final VoidCallback onTap;

  const _PaymentMethodButton({
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
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFF2E7D32) : Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(
            color: isSelected ? const Color(0xFF2E7D32) : Colors.grey.shade300,
          ),
        ),
        child: Column(
          children: [
            Icon(
              icon,
              color: isSelected ? Colors.white : Colors.grey.shade600,
              size: 24,
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: TextStyle(
                color: isSelected ? Colors.white : Colors.grey.shade600,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
