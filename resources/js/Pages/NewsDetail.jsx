import { Head, Link, usePage } from '@inertiajs/react';
import SiteLayout from '../Layouts/SiteLayout';

const catLabel = (cat, en) => ({
    pembaruan_korporasi: 'Investor Update',
    edukasi_gaya_hidup: en ? 'Health Information' : 'Informasi Kesehatan',
    informasi_produk: en ? 'Product Info' : 'Info Produk',
    lainnya: 'Others',
}[cat] || (en ? 'News' : 'Berita'));

export default function NewsDetail({ article, others }) {
    const { props: { locale, nav } } = usePage();
    const en = locale === 'en';
    const readMore = en ? 'Read More' : 'Baca Selengkapnya';

    // Estimate read time from body word count (~200 wpm).
    const words = article.body ? article.body.replace(/<[^>]+>/g, ' ').trim().split(/\s+/).filter(Boolean).length : 0;
    const readMin = words ? Math.max(1, Math.round(words / 200)) : null;

    const shareUrl = encodeURIComponent(typeof window !== 'undefined' ? window.location.href : '');
    const shareText = encodeURIComponent(article.title);
    const shares = [
        { label: 'Facebook', href: `https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`, path: 'M13.5 21v-7h2.4l.4-2.8h-2.8V9.4c0-.8.2-1.4 1.4-1.4h1.5V5.5c-.3 0-1.2-.1-2.2-.1-2.2 0-3.7 1.3-3.7 3.8v2H8.1V14h2.4v7h3Z' },
        { label: 'X', href: `https://twitter.com/intent/tweet?url=${shareUrl}&text=${shareText}`, path: 'M17.5 3h3l-6.6 7.5L21.6 21h-5.7l-4.2-5.5L6.8 21H3.8l7-8L2.9 3h5.8l3.8 5 4.9-5Zm-1 16h1.6L8.1 4.6H6.4L16.5 19Z' },
        { label: 'LinkedIn', href: `https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl}`, path: 'M6.5 8.8H3.6V20h2.9V8.8ZM5 7.4a1.7 1.7 0 1 0 0-3.4 1.7 1.7 0 0 0 0 3.4ZM20.4 20v-5.9c0-3.2-1.7-4.7-4-4.7a3.4 3.4 0 0 0-3.1 1.7V8.8h-2.9V20h2.9v-5.9c0-1.5.7-2.4 2-2.4s1.9.9 1.9 2.4V20h3.2Z' },
        { label: 'WhatsApp', href: `https://api.whatsapp.com/send?text=${shareText}%20${shareUrl}`, path: 'M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2Zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .3-3.4-.7-2.9-1.2-4.7-4.2-4.8-4.4-.1-.2-1.1-1.5-1.1-2.8s.7-2 .9-2.2c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.4.6c-.2.2-.3.4-.1.7.2.3.9 1.4 1.9 2.3 1.3 1.1 2.3 1.5 2.6 1.6.3.1.5.1.7-.1l.9-1c.2-.3.4-.2.7-.1l2 .9c.3.1.5.2.5.4.1.2.1.9-.1 1.4Z' },
        { label: 'Email', href: `mailto:?subject=${shareText}&body=${shareUrl}`, path: 'M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm.5 2 7.5 4.7L19.5 7H4.5Z' },
    ];

    return (
        <>
            <Head title={`${article.title} — Combiphar`} />

            <section className="detail-banner" style={article.cover ? { backgroundImage: `url('${article.cover}')` } : {}} aria-label={article.title}></section>

            <section className="section">
                <div className="container article-layout">
                    <article className="article-body rv">
                        <h1 className="article__title">{article.title}</h1>
                        <p className="article__meta">
                            <span>{article.datetime || article.date}</span>
                            {readMin && (
                                <>
                                    <span className="article__meta-sep">|</span>
                                    <svg className="article__clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" strokeLinecap="round" strokeLinejoin="round" /></svg>
                                    <span>{readMin} {en ? 'min read' : 'menit baca'}</span>
                                </>
                            )}
                        </p>

                        {article.excerpt && <p className="article-lead">{article.excerpt}</p>}
                        <div dangerouslySetInnerHTML={{ __html: article.body || '' }} />

                        <div className="article__foot">
                            <p className="article__cat"><strong>{en ? 'Category:' : 'Kategori:'}</strong> {catLabel(article.category, en)}</p>
                            <div className="article__share">
                                <span>{en ? 'Share:' : 'Bagikan:'}</span>
                                <div className="article__share-icons">
                                    {shares.map((s) => (
                                        <a key={s.label} href={s.href} target="_blank" rel="noopener noreferrer" aria-label={(en ? 'Share on ' : 'Bagikan ke ') + s.label}>
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d={s.path} /></svg>
                                        </a>
                                    ))}
                                </div>
                            </div>
                        </div>

                        <Link className="btn btn--outline" href={nav.news} style={{ marginTop: 32 }}>&larr; {en ? 'Back to News' : 'Kembali ke Berita'}</Link>
                    </article>

                    <aside className="article-aside rv">
                        <h2>{en ? 'Other News' : 'Berita Lainnya'}</h2>
                        {others.map((o) => (
                            <article className="ncard" key={o.slug}>
                                <div className="ncard__img" style={o.cover ? { backgroundImage: `url('${o.cover}')` } : {}}></div>
                                <div className="ncard__body">
                                    <span className="ncard__date">{o.date}</span>
                                    <h3 className="ncard__title">{o.title}</h3>
                                    <Link className="ncard__btn" href={o.url}>{readMore}</Link>
                                </div>
                            </article>
                        ))}
                    </aside>
                </div>
            </section>
        </>
    );
}

NewsDetail.layout = (page) => <SiteLayout>{page}</SiteLayout>;
