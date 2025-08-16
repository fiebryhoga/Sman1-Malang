import React from "react";

const Home = () => {
    return (
        <div
            className="bg-gray-50"
            style={{ fontFamily: "'Poppins', sans-serif" }}
        >
            {/* Menambahkan Google Font Poppins langsung di sini */}
            <style>
                {`
          @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
        `}
            </style>

            {/* Header */}
            {/* <header className="absolute top-0 left-0 w-full z-10 p-4 sm:p-6">
                <div className="container mx-auto flex justify-between items-center">
                    <h1 className="text-xl font-bold text-gray-800">
                        SIMAPRES
                    </h1>
                    <a
                        href="/admin"
                        className="hidden sm:inline-block bg-white text-blue-600 font-semibold py-2 px-4 border border-gray-200 rounded-lg shadow-sm hover:bg-gray-100 transition duration-300"
                    >
                        Login Admin
                    </a>
                </div>
            </header> */}

            {/* Hero Section */}
            <main className="relative md:h-screen flex items-end justify-center overflow-hidden">
                <div className="container h-full mx-auto px-6 pt-12 md:py-0 lg:flex lg:items-end lg:gap-x-10">
                    <div className="mx-auto h-full max-w-2xl lg:mx-0 lg:flex flex-col  lg:order-2 justify-center items-start">
                        <div className="flex">
                            <div className="relative rounded-full px-3 py-1 text-sm leading-6 text-gray-500 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                                SMKN 1 Sukorejo
                            </div>
                        </div>
                        <h2
                            className="mt-10 text-3xl font-bold tracking-tight text-blue-900/100 sm:text-6xl"
                        >
                            Manajemen Presensi & Poin Siswa
                        </h2>
                        <p className="mt-6 text-sm md:text-lg md:leading-8 text-gray-600">
                            SIMAPRES adalah solusi digital terintegrasi untuk
                            memantau kehadiran dan mengelola poin pelanggaran
                            siswa secara efisien, transparan, dan real-time.
                        </p>
                        <div className="mt-10 flex items-center gap-x-6">
                            <a
                                href="/admin"
                                className="rounded-md bg-blue-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition duration-300"
                            >
                                Mulai Kelola
                            </a>
                            <a
                                href="#"
                                className="text-sm font-semibold leading-6 text-gray-900"
                            >
                                Pelajari lebih lanjut{" "}
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>

                    <div className="mt-16 sm:mt-24 lg:mt-0 lg:flex-shrink-0 lg:flex-grow lg:order-1">
                        <img
                            src="/img/model.png"
                            alt="Ilustrasi Manajemen Sekolah"
                            className="mx-auto w-[32rem] max-w-full drop-shadow-xl"
                        />
                    </div>
                </div>
            </main>
        </div>
    );
};

export default Home;
