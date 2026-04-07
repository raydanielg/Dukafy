import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../storage/storage_providers.dart';

const kDefaultApiBaseUrl = 'http://10.118.138.203:8000/api';

String _normalizeBaseUrl(String raw) {
  var url = raw.trim();
  if (url.isEmpty) return '';

  if (!url.startsWith('http://') && !url.startsWith('https://')) {
    url = 'http://$url';
  }

  url = url.replaceAll(RegExp(r'/+$'), '');

  if (!url.endsWith('/api')) {
    url = '$url/api';
  }

  return url;
}

class ApiClient {
  ApiClient({required Dio dio}) : _dio = dio;

  final Dio _dio;

  Dio get dio => _dio;
}

final dioProvider = Provider<Dio>((ref) {
  final dio = Dio(
    BaseOptions(
      baseUrl: kDefaultApiBaseUrl,
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 30),
      headers: {
        'Accept': 'application/json',
      },
    ),
  );

  dio.interceptors.add(
    InterceptorsWrapper(
      onRequest: (options, handler) async {
        final storage = ref.read(secureStorageProvider);
        final rawBaseUrl = await storage.getApiBaseUrl();
        if (rawBaseUrl != null && rawBaseUrl.trim().isNotEmpty) {
          final normalized = _normalizeBaseUrl(rawBaseUrl);
          if (normalized.isNotEmpty) {
            options.baseUrl = normalized;
          }
        }

        final token = await storage.getAuthToken();
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }

        return handler.next(options);
      },
    ),
  );

  return dio;
});

final apiClientProvider = Provider<ApiClient>((ref) {
  return ApiClient(dio: ref.watch(dioProvider));
});
