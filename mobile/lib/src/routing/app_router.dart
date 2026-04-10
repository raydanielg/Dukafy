import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../features/splash/splash_screen.dart';
import '../features/onboarding/onboarding_screen.dart';
import '../features/auth/login_screen.dart';
import '../features/auth/register_screen.dart';
import '../features/auth/forgot_password_screen.dart';
import '../features/auth/approval_screen.dart';
import '../features/dashboard/dashboard_screen.dart';
import '../features/profile/profile_screen.dart';
import '../features/pos/pos_screen.dart';
import '../features/products/products_screen.dart';
import '../features/purchases/purchases_screen.dart';
import '../features/invoices/invoices_screen.dart';
import '../features/articles/articles_screen.dart';
import '../features/articles/article_details_screen.dart';
import '../features/shops/shops_screen.dart';
import '../features/members/members_screen.dart';
import '../features/customers/customers_screen.dart';
import '../features/suppliers/suppliers_screen.dart';
import '../features/loans/loans_screen.dart';
import '../features/expenses/expenses_screen.dart';
import '../features/banking/banking_screen.dart';
import '../features/notifications/notifications_screen.dart';
import '../features/reports/reports_screen.dart';
import '../features/sales/sale_screen.dart';
import '../features/payments/payment_screen.dart';

final appRouterProvider = Provider<GoRouter>((ref) {
  return GoRouter(
    initialLocation: SplashScreen.routePath,
    routes: [
      GoRoute(
        path: SplashScreen.routePath,
        name: SplashScreen.routeName,
        builder: (context, state) => const SplashScreen(),
      ),
      GoRoute(
        path: OnboardingScreen.routePath,
        name: OnboardingScreen.routeName,
        builder: (context, state) => const OnboardingScreen(),
      ),
      GoRoute(
        path: LoginScreen.routePath,
        name: LoginScreen.routeName,
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: RegisterScreen.routePath,
        name: RegisterScreen.routeName,
        builder: (context, state) => const RegisterScreen(),
      ),
      GoRoute(
        path: ForgotPasswordScreen.routePath,
        name: ForgotPasswordScreen.routeName,
        builder: (context, state) => const ForgotPasswordScreen(),
      ),
      GoRoute(
        path: ApprovalScreen.routePath,
        name: ApprovalScreen.routeName,
        builder: (context, state) {
          final extra = state.extra;
          final map = extra is Map ? extra : const <String, dynamic>{};

          return ApprovalScreen(
            name: (map['name'] as String?) ?? '',
            phone: (map['phone'] as String?) ?? '',
          );
        },
      ),
      GoRoute(
        path: DashboardScreen.routePath,
        name: DashboardScreen.routeName,
        builder: (context, state) => const DashboardScreen(),
      ),
      GoRoute(
        path: ProfileScreen.routePath,
        name: ProfileScreen.routeName,
        builder: (context, state) => const ProfileScreen(),
      ),
      GoRoute(
        path: POSScreen.routePath,
        name: POSScreen.routeName,
        builder: (context, state) => const POSScreen(),
      ),
      GoRoute(
        path: ProductsScreen.routePath,
        name: ProductsScreen.routeName,
        builder: (context, state) => const ProductsScreen(),
      ),
      GoRoute(
        path: PurchasesScreen.routePath,
        name: PurchasesScreen.routeName,
        builder: (context, state) => const PurchasesScreen(),
      ),
      GoRoute(
        path: InvoicesScreen.routePath,
        name: InvoicesScreen.routeName,
        builder: (context, state) => const InvoicesScreen(),
      ),
      GoRoute(
        path: ArticlesScreen.routePath,
        name: ArticlesScreen.routeName,
        builder: (context, state) => const ArticlesScreen(),
      ),
      GoRoute(
        path: ArticleDetailsScreen.routePath,
        name: ArticleDetailsScreen.routeName,
        builder: (context, state) {
          final id = state.pathParameters['id']!;
          return ArticleDetailsScreen(id: id);
        },
      ),
      GoRoute(
        path: ShopsScreen.routePath,
        name: ShopsScreen.routeName,
        builder: (context, state) => const ShopsScreen(),
      ),
      GoRoute(
        path: MembersScreen.routePath,
        name: MembersScreen.routeName,
        builder: (context, state) => const MembersScreen(),
      ),
      GoRoute(
        path: CustomersScreen.routePath,
        name: CustomersScreen.routeName,
        builder: (context, state) => const CustomersScreen(),
      ),
      GoRoute(
        path: SuppliersScreen.routePath,
        name: SuppliersScreen.routeName,
        builder: (context, state) => const SuppliersScreen(),
      ),
      GoRoute(
        path: LoansScreen.routePath,
        name: LoansScreen.routeName,
        builder: (context, state) => const LoansScreen(),
      ),
      GoRoute(
        path: ExpensesScreen.routePath,
        name: ExpensesScreen.routeName,
        builder: (context, state) => const ExpensesScreen(),
      ),
      GoRoute(
        path: BankingScreen.routePath,
        name: BankingScreen.routeName,
        builder: (context, state) => const BankingScreen(),
      ),
      GoRoute(
        path: NotificationsScreen.routePath,
        name: NotificationsScreen.routeName,
        builder: (context, state) => const NotificationsScreen(),
      ),
      GoRoute(
        path: ReportsScreen.routePath,
        name: ReportsScreen.routeName,
        builder: (context, state) => const ReportsScreen(),
      ),
      GoRoute(
        path: SaleScreen.routePath,
        name: SaleScreen.routeName,
        builder: (context, state) => const SaleScreen(),
      ),
      GoRoute(
        path: PaymentScreen.routePath,
        name: PaymentScreen.routeName,
        builder: (context, state) => const PaymentScreen(),
      ),
    ],
    errorBuilder: (context, state) => Scaffold(
      body: Center(child: Text(state.error.toString())),
    ),
  );
});
