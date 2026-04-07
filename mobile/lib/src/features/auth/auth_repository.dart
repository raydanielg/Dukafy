import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/api/api_client.dart';
import '../../core/storage/storage_providers.dart';

class AuthResult {
  const AuthResult({
    required this.name,
    required this.phone,
    required this.isApproved,
  });

  final String name;
  final String phone;
  final bool isApproved;
}

class AuthRepository {
  AuthRepository(this._ref);

  final Ref _ref;

  String _normalizePhone(String phone) {
    final p = phone.trim().replaceAll(' ', '');
    if (p.startsWith('+')) return p;
    if (p.startsWith('0') && p.length >= 10) {
      return '+255${p.substring(1)}';
    }
    if (p.startsWith('255')) return '+$p';
    return p;
  }

  String _friendlyDioError(Object e) {
    if (e is! DioException) return 'Something went wrong. Please try again.';

    switch (e.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        return 'Request timed out. Check your internet / API Base URL then try again.';
      case DioExceptionType.connectionError:
        return 'Cannot reach server. Check API Base URL and connection.';
      case DioExceptionType.badResponse:
        final data = e.response?.data;
        if (data is Map && data['message'] is String) {
          return data['message'] as String;
        }
        return 'Server error. Please try again.';
      case DioExceptionType.cancel:
        return 'Request cancelled.';
      case DioExceptionType.badCertificate:
        return 'Bad certificate.';
      case DioExceptionType.unknown:
        return 'Network error. Please try again.';
    }
  }

  Future<AuthResult> login({required String phone, required String password}) async {
    final dio = _ref.read(apiClientProvider).dio;
    try {
      final res = await dio.post(
        '/auth/login',
        data: {
          'phone': _normalizePhone(phone),
          'password': password,
        },
      );

      final data = res.data;
      final token = (data as Map)['access_token'] as String?;
      if (token == null || token.isEmpty) {
        throw DioException(
          requestOptions: res.requestOptions,
          response: res,
          message: 'Missing token',
        );
      }

      await _ref.read(secureStorageProvider).setAuthToken(token);

      final user = (data)['user'];
      if (user is Map) {
        return AuthResult(
          name: (user['name'] as String?) ?? '',
          phone: (user['phone'] as String?) ?? '',
          isApproved: (user['is_approved'] == true),
        );
      }

      return const AuthResult(name: '', phone: '', isApproved: false);
    } catch (e) {
      throw Exception(_friendlyDioError(e));
    }
  }

  Future<AuthResult> register({
    required String name,
    required String phone,
    required String password,
  }) async {
    final dio = _ref.read(apiClientProvider).dio;
    try {
      final res = await dio.post(
        '/auth/register',
        data: {
          'name': name,
          'phone': _normalizePhone(phone),
          'password': password,
        },
      );

      final data = res.data;
      final token = (data as Map)['access_token'] as String?;
      if (token == null || token.isEmpty) {
        throw DioException(
          requestOptions: res.requestOptions,
          response: res,
          message: 'Missing token',
        );
      }

      await _ref.read(secureStorageProvider).setAuthToken(token);

      final user = (data)['user'];
      if (user is Map) {
        return AuthResult(
          name: (user['name'] as String?) ?? name,
          phone: (user['phone'] as String?) ?? _normalizePhone(phone),
          isApproved: (user['is_approved'] == true),
        );
      }

      return AuthResult(
        name: name,
        phone: _normalizePhone(phone),
        isApproved: false,
      );
    } catch (e) {
      throw Exception(_friendlyDioError(e));
    }
  }

  Future<void> logout() async {
    final dio = _ref.read(apiClientProvider).dio;
    try {
      await dio.post('/auth/logout');
    } catch (_) {
      // ignore
    }
    await _ref.read(secureStorageProvider).clearAuthToken();
  }
}

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return AuthRepository(ref);
});
