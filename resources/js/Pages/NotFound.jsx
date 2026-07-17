import { Head, Link, usePage } from "@inertiajs/react"
import SiteLayout from "../Layouts/SiteLayout"

export default function NotFound() {
  const {
    props: { t, homeUrl },
  } = usePage()
  const nf = t?.notfound

  return (
    <>
      <Head title={`${nf?.code || "404 Not Found"} — Combiphar`}>
        <meta head-key="robots" name="robots" content="noindex, follow" />
      </Head>

      <section className="notfound">
        <div className="container">
          <h1 className="notfound__code">{nf?.code || "404 Not Found"}</h1>
          <p className="notfound__title">
            {nf?.title || "Maaf, halaman ini tidak ditemukan"}
          </p>
          <p className="notfound__text">
            {nf?.text ||
              "Halaman yang Anda cari mungkin telah dihapus, telah diubah namanya atau sedang tidak tersedia saat ini"}
          </p>
          <Link className="notfound__btn" href={homeUrl || "/"}>
            {nf?.home || "Kembali ke Beranda"}
          </Link>
        </div>
      </section>
    </>
  )
}

NotFound.layout = (page) => <SiteLayout>{page}</SiteLayout>
