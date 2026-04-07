import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorage {
  SecureStorage(this._storage);

  final FlutterSecureStorage _storage;

  static const onboardingDoneKey = 'onboarding_done';
  static const localeKey = 'locale';

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
}
