import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../storage/storage_providers.dart';

class LocaleController extends Notifier<Locale> {
  @override
  Locale build() {
    Future.microtask(_load);
    return const Locale('en');
  }

  Future<void> _load() async {
    final code = await ref.read(secureStorageProvider).getLocaleCode();
    if (code == null || code.isEmpty) return;
    state = Locale(code);
  }

  Future<void> setLocale(Locale locale) async {
    state = locale;
    await ref.read(secureStorageProvider).setLocaleCode(locale.languageCode);
  }
}

final localeControllerProvider = NotifierProvider<LocaleController, Locale>(
  LocaleController.new,
);
