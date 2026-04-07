import 'package:flutter_riverpod/flutter_riverpod.dart';

class UserState {
  final Map<String, dynamic>? data;
  final bool isLoading;

  UserState({this.data, this.isLoading = false});

  UserState copyWith({Map<String, dynamic>? data, bool? isLoading}) {
    return UserState(
      data: data ?? this.data,
      isLoading: isLoading ?? this.isLoading,
    );
  }
}

class UserNotifier extends StateNotifier<UserState> {
  UserNotifier() : super(UserState(isLoading: true));

  void setUser(Map<String, dynamic> data) {
    state = state.copyWith(data: data, isLoading: false);
  }

  void setLoading(bool loading) {
    state = state.copyWith(isLoading: loading);
  }

  void updateAvatar(String url) {
    if (state.data != null) {
      final newData = Map<String, dynamic>.from(state.data!);
      newData['avatar_url'] = url;
      state = state.copyWith(data: newData);
    }
  }

  void updateBusinessLogo(String url) {
    if (state.data != null && state.data!['business'] != null) {
      final newData = Map<String, dynamic>.from(state.data!);
      final business = Map<String, dynamic>.from(newData['business']);
      business['logo_url'] = url;
      newData['business'] = business;
      state = state.copyWith(data: newData);
    }
  }
}

final userProvider = StateNotifierProvider<UserNotifier, UserState>((ref) {
  return UserNotifier();
});
