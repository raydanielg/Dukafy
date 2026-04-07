import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/api/api_client.dart';
import '../../core/storage/storage_providers.dart';

class AuthRepository {
  AuthRepository(this._ref);

  final Ref _ref;

  Future<void> login({required String phone, required String password}) async {
    final dio = _ref.read(apiClientProvider).dio;
    final res = await dio.post(
      '/auth/login',
      data: {
        'phone': phone,
        'password': password,
      },
    );

    final token = (res.data as Map)['access_token'] as String?;
    if (token == null || token.isEmpty) {
      throw DioException(
        requestOptions: res.requestOptions,
        response: res,
        message: 'Missing token',
      );
    }

    await _ref.read(secureStorageProvider).setAuthToken(token);
  }

  Future<void> register({
    required String name,
    required String phone,
    required String password,
  }) async {
    final dio = _ref.read(apiClientProvider).dio;
    final res = await dio.post(
      '/auth/register',
      data: {
        'name': name,
        'phone': phone,
        'password': password,
      },
    );

    final token = (res.data as Map)['access_token'] as String?;
    if (token == null || token.isEmpty) {
      throw DioException(
        requestOptions: res.requestOptions,
        response: res,
        message: 'Missing token',
      );
    }

    await _ref.read(secureStorageProvider).setAuthToken(token);
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
