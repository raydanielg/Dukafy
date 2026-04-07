import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../storage/storage_providers.dart';

class LocaleController extends StateNotifier<Locale> {
  LocaleController(this._ref) : super(const Locale('en')) {
    _load();
  }

  final Ref _ref;

  Future<void> _load() async {
    final code = await _ref.read(secureStorageProvider).getLocaleCode();
    if (code == null || code.isEmpty) return;
    state = Locale(code);
  }

  Future<void> setLocale(Locale locale) async {
    state = locale;
    await _ref.read(secureStorageProvider).setLocaleCode(locale.languageCode);
  }
}

final localeControllerProvider =
    StateNotifierProvider<LocaleController, Locale>((ref) {
  return LocaleController(ref);
});
