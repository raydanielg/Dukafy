import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_html/flutter_html.dart';
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
    final title = _article?['title']?.toString();
    final contentHtml = _article?['content']?.toString();

    return Scaffold(
      body: Skeletonizer(
        enabled: _isLoading,
        child: CustomScrollView(
          slivers: [
            SliverAppBar(
              expandedHeight: 250,
              pinned: true,
              backgroundColor: primaryGreen,
              foregroundColor: Colors.white,
              flexibleSpace: FlexibleSpaceBar(
                title: Text(
                  title ?? '',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14),
                ),
                background: Stack(
                  fit: StackFit.expand,
                  children: [
                    if (_article?['image_url'] != null)
                      Image.network(
                        _article['image_url'],
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) {
                          return Container(color: Colors.grey.shade200);
                        },
                      )
                    else
                      Container(color: Colors.grey.shade200),
                    DecoratedBox(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [
                            Colors.black.withOpacity(0.15),
                            Colors.black.withOpacity(0.55),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (_article?['category'] != null)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: primaryGreen.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(999),
                        ),
                        child: Text(
                          _article['category']['name'].toString().toUpperCase(),
                          style: TextStyle(
                            color: primaryGreen,
                            fontWeight: FontWeight.w800,
                            letterSpacing: 1.0,
                            fontSize: 11,
                          ),
                        ),
                      ),
                    const SizedBox(height: 10),
                    Text(
                      title ?? 'Loading article title...',
                      style: const TextStyle(fontSize: 26, fontWeight: FontWeight.w900, height: 1.15),
                    ),
                    const Divider(height: 40),
                    if (contentHtml == null)
                      const Text(
                        'Loading content...',
                        style: TextStyle(fontSize: 16, height: 1.6, color: Colors.black87),
                      )
                    else
                      Html(
                        data: contentHtml,
                        style: {
                          'body': Style(
                            margin: Margins.zero,
                            padding: HtmlPaddings.zero,
                            fontSize: FontSize(16),
                            lineHeight: const LineHeight(1.65),
                            color: Colors.black87,
                          ),
                          'p': Style(margin: Margins.only(bottom: 14)),
                          'h1': Style(fontSize: FontSize(24), fontWeight: FontWeight.w900, margin: Margins.only(bottom: 12, top: 8)),
                          'h2': Style(fontSize: FontSize(22), fontWeight: FontWeight.w900, margin: Margins.only(bottom: 12, top: 8)),
                          'h3': Style(fontSize: FontSize(18), fontWeight: FontWeight.w900, margin: Margins.only(bottom: 10, top: 6)),
                          'ul': Style(margin: Margins.only(bottom: 14, left: 18)),
                          'ol': Style(margin: Margins.only(bottom: 14, left: 18)),
                          'li': Style(margin: Margins.only(bottom: 10)),
                          'blockquote': Style(
                            padding: HtmlPaddings.all(14),
                            margin: Margins.only(bottom: 16),
                            backgroundColor: primaryGreen.withOpacity(0.08),
                            border: Border(left: BorderSide(color: primaryGreen, width: 4)),
                            fontStyle: FontStyle.italic,
                          ),
                          'strong': Style(fontWeight: FontWeight.w900),
                        },
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
