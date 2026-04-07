import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../core/api/api_client.dart';

class ArticlesScreen extends ConsumerStatefulWidget {
  const ArticlesScreen({super.key});

  static const routeName = 'articles';
  static const routePath = '/articles';

  @override
  ConsumerState<ArticlesScreen> createState() => _ArticlesScreenState();
}

class _ArticlesScreenState extends ConsumerState<ArticlesScreen> {
  bool _isLoading = true;
  List<dynamic> _articles = [];
  final Color primaryGreen = const Color(0xFF2E7D32);

  @override
  void initState() {
    super.initState();
    _fetchArticles();
  }

  Future<void> _fetchArticles() async {
    try {
      final dio = ref.read(apiClientProvider).dio;
      final res = await dio.get('/auth/articles');
      if (mounted) {
        setState(() {
          _articles = res.data['data'] ?? [];
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error loading articles: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Articles & Tips', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: primaryGreen,
        foregroundColor: Colors.white,
      ),
      body: Skeletonizer(
        enabled: _isLoading,
        child: RefreshIndicator(
          onRefresh: _fetchArticles,
          color: primaryGreen,
          child: _articles.isEmpty && !_isLoading
              ? Padding(
                  padding: const EdgeInsets.all(24),
                  child: Center(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.article_outlined, size: 56, color: primaryGreen.withOpacity(0.6)),
                        const SizedBox(height: 12),
                        const Text(
                          'No articles yet',
                          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          'Pull down to refresh, or check back later.',
                          textAlign: TextAlign.center,
                          style: TextStyle(color: Colors.black.withOpacity(0.6)),
                        ),
                      ],
                    ),
                  ),
                )
              : GridView.builder(
                  padding: const EdgeInsets.all(16),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2,
                    crossAxisSpacing: 12,
                    mainAxisSpacing: 12,
                    childAspectRatio: 0.72,
                  ),
                  itemCount: _isLoading ? 6 : _articles.length,
                  itemBuilder: (context, index) {
                    final article = _isLoading ? null : _articles[index];
                    return _ArticleCard(article: article, primaryGreen: primaryGreen);
                  },
                ),
        ),
      ),
    );
  }
}

class _ArticleCard extends StatelessWidget {
  final dynamic article;
  final Color primaryGreen;

  const _ArticleCard({this.article, required this.primaryGreen});

  @override
  Widget build(BuildContext context) {
    final title = (article is Map) ? (article['title']?.toString()) : null;
    final excerpt = (article is Map) ? (article['excerpt']?.toString()) : null;

    return Card(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: article == null ? null : () => context.push('/articles/${article['id']}'),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              height: 180,
              width: double.infinity,
              color: Colors.grey.shade200,
              child: (article?['image_url'] != null)
                  ? Image.network(
                      article['image_url'],
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return const Center(
                          child: Icon(Icons.broken_image_outlined, size: 40, color: Colors.grey),
                        );
                      },
                    )
                  : const Center(
                      child: Icon(Icons.article_outlined, size: 40, color: Colors.grey),
                    ),
            ),
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (article?['category'] != null)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: primaryGreen.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Text(
                        article['category']['name'].toString().toUpperCase(),
                        style: TextStyle(color: primaryGreen, fontSize: 10, fontWeight: FontWeight.bold),
                      ),
                    ),
                  const SizedBox(height: 8),
                  Text(
                    title ?? 'Loading article title...',
                    style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900),
                    maxLines: 3,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    (excerpt != null && excerpt.isNotEmpty)
                        ? excerpt
                        : 'Loading article summary and content details...',
                    style: TextStyle(color: Colors.grey.shade600, fontSize: 12, height: 1.35),
                    maxLines: 3,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
