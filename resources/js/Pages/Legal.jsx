import { Head, usePage } from "@inertiajs/react"
import SiteLayout from "../Layouts/SiteLayout"

export default function Legal({ title, body }) {
  const {
    props: { t, routeName },
  } = usePage()
  const fallback = routeName === "privacy" ? t.privacy : t.terms
  const heading = title || fallback

  return (
    <>
      <Head title={`${heading} — Combiphar`} />

      <section className="banner banner--about banner--legal">
        <div className="container">
          <h1 className="display">{heading}</h1>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <article
            className="article-body legal-body rv"
            dangerouslySetInnerHTML={{ __html: body || "" }}
          />
        </div>
      </section>
    </>
  )
}

Legal.layout = (page) => <SiteLayout>{page}</SiteLayout>
