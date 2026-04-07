import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../core/api/api_client.dart';

class ArticleDetailsScreen extends ConsumerStatefulWidget {
  final String id;
  const ArticleDetailsScreen({super.key, required this.id});

  static const routeName = 'article-details';
  static const routePath = '/articles/:id';

  @override
  ConsumerState<ArticleDetailsScreen> createState() => _ArticleDetailsScreenState();
}

class _ArticleDetailsScreenState extends ConsumerState<ArticleDetailsScreen> {
  bool _isLoading = true;
  dynamic _article;
  final Color primaryGreen = const Color(0xFF2E7D32);

  @override
  void initState() {
    super.initState();
    _fetchDetails();
  }

  Future<void> _fetchDetails() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/articles/${widget.id}');
      if (mounted) {
        setState(() {
          _article = res.data['article'];
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Skeletonizer(
        enabled: _isLoading,
        child: CustomScrollView(
          slivers: [
            SliverAppBar(
              expandedHeight: 250,
              pinned: true,
              backgroundColor: primaryGreen,
              flexibleSpace: FlexibleSpaceBar(
                background: _article?['image_url'] != null
                    ? Image.network(_article['image_url'], fit: BoxFit.cover)
                    : Container(color: Colors.grey.shade200),
              ),
            ),
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (_article?['category'] != null)
                      Text(
                        _article['category']['name'].toString().toUpperCase(),
                        style: TextStyle(color: primaryGreen, fontWeight: FontWeight.bold, letterSpacing: 1.2),
                      ),
                    const SizedBox(height: 10),
                    Text(
                      _article?['title'] ?? 'Loading article title...',
                      style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900),
                    ),
                    const Divider(height: 40),
                    Text(
                      _article?['content'] ?? 'Loading content...',
                      style: const TextStyle(fontSize: 16, height: 1.6, color: Colors.black87),
                    ),
                    const SizedBox(height: 50),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
