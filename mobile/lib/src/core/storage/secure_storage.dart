import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorage {
  SecureStorage(this._storage);

  final FlutterSecureStorage _storage;

  static const onboardingDoneKey = 'onboarding_done';
  static const localeKey = 'locale';
  static const apiBaseUrlKey = 'api_base_url';
  static const authTokenKey = 'auth_token';

  Future<bool> isOnboardingDone() async {
    final value = await _storage.read(key: onboardingDoneKey);
    return value == '1';
  }

  Future<void> setOnboardingDone() async {
    await _storage.write(key: onboardingDoneKey, value: '1');
  }

  Future<String?> getLocaleCode() async {
    return _storage.read(key: localeKey);
  }

  Future<void> setLocaleCode(String code) async {
    await _storage.write(key: localeKey, value: code);
  }

  Future<String?> getApiBaseUrl() async {
    return _storage.read(key: apiBaseUrlKey);
  }

  Future<void> setApiBaseUrl(String url) async {
    await _storage.write(key: apiBaseUrlKey, value: url);
  }

  Future<String?> getAuthToken() async {
    return _storage.read(key: authTokenKey);
  }

  Future<void> setAuthToken(String token) async {
    await _storage.write(key: authTokenKey, value: token);
  }

  Future<void> clearAuthToken() async {
    await _storage.delete(key: authTokenKey);
  }
}
