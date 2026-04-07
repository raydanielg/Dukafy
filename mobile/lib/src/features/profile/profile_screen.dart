import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import '../../core/api/api_client.dart';

class ProfileScreen extends ConsumerStatefulWidget {
  const ProfileScreen({super.key});

  static const routeName = 'profile';
  static const routePath = '/profile';

  @override
  ConsumerState<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends ConsumerState<ProfileScreen> {
  bool _isLoading = false;
  Map<String, dynamic>? _user;
  final Color primaryGreen = const Color(0xFF2E7D32);

  @override
  void initState() {
    super.initState();
    _loadUser();
  }

  Future<void> _loadUser() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/me');
      setState(() {
        _user = res.data['user'];
      });
    } catch (_) {}
  }

  Future<void> _pickAndUploadImage(bool isAvatar) async {
    final picker = ImagePicker();
    final image = await picker.pickImage(source: ImageSource.gallery, maxWidth: 512);
    
    if (image == null) return;

    setState(() => _isLoading = true);

    try {
      final dio = ref.read(apiClientProvider).dio;
      final formData = FormData.fromMap({
        isAvatar ? 'avatar' : 'logo': await MultipartFile.fromFile(image.path),
        if (isAvatar) 'name': _user?['name'] ?? '',
      });

      final url = isAvatar ? '/auth/update-profile' : '/auth/update-business-logo';
      await dio.post(url, data: formData);
      
      await _loadUser();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('${isAvatar ? "Avatar" : "Logo"} updated successfully!')),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Upload failed: $e')),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_user == null) return const Scaffold(body: Center(child: CircularProgressIndicator()));

    return Scaffold(
      appBar: AppBar(
        title: const Text('My Profile', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: primaryGreen,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            const SizedBox(height: 20),
            // User Avatar Section
            Center(
              child: Stack(
                children: [
                  CircleAvatar(
                    radius: 60,
                    backgroundColor: primaryGreen.withOpacity(0.1),
                    backgroundImage: _user?['avatar_url'] != null ? NetworkImage(_user!['avatar_url']) : null,
                    child: _user?['avatar_url'] == null 
                      ? Text(_user?['name']?[0].toUpperCase() ?? 'U', style: TextStyle(fontSize: 40, color: primaryGreen, fontWeight: FontWeight.bold))
                      : null,
                  ),
                  Positioned(
                    bottom: 0,
                    right: 0,
                    child: FloatingActionButton.small(
                      heroTag: 'avatar_btn',
                      backgroundColor: Colors.white,
                      onPressed: () => _pickAndUploadImage(true),
                      child: Icon(Icons.camera_alt, color: primaryGreen),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 10),
            Text(_user?['name'] ?? '', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
            Text(_user?['phone'] ?? '', style: const TextStyle(color: Colors.grey)),
            
            const SizedBox(height: 40),
            const Divider(),
            
            // Business Logo Section
            if (_user?['business'] != null) ...[
              const SizedBox(height: 20),
              const Align(alignment: Alignment.centerLeft, child: Text('Business Logo', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold))),
              const SizedBox(height: 15),
              Center(
                child: Stack(
                  children: [
                    Container(
                      width: 150,
                      height: 100,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade100,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: Colors.grey.shade300),
                      ),
                      child: _user?['business']?['logo_url'] != null
                        ? ClipRRect(
                            borderRadius: BorderRadius.circular(12),
                            child: Image.network(_user!['business']['logo_url'], fit: BoxFit.contain),
                          )
                        : const Center(child: Icon(Icons.storefront, size: 40, color: Colors.grey)),
                    ),
                    Positioned(
                      bottom: -5,
                      right: -5,
                      child: FloatingActionButton.small(
                        heroTag: 'logo_btn',
                        backgroundColor: Colors.white,
                        onPressed: () => _pickAndUploadImage(false),
                        child: Icon(Icons.edit, color: primaryGreen),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 10),
              Text(_user?['business']?['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold)),
            ],
            
            if (_isLoading) ...[
              const SizedBox(height: 20),
              const LinearProgressIndicator(),
            ]
          ],
        ),
      ),
    );
  }
}
