<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O-LDPMS | Online Lumber Dealer Permitting & Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="logo/denr_logo.png" type="image/x-icon">
    <link rel="icon" type="logo/denr_logo.png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="logo/denr_logo.png" sizes="16x16" href="favicon-16x16.png">

    <meta name="theme-color" content="#064e3b">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .bg-denr { background-color: #064e3b; } /* Deep Forest Green */
        .text-denr { color: #064e3b; }
        .hero-pattern {
            background-image: linear-gradient(rgba(6, 78, 59, 0.9), rgba(6, 78, 59, 0.8)), url('https://images.unsplash.com/photo-1589939705384-5185137a7f0f?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-slate-50">

    <nav class="sticky top-0 z-50 bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <img src="logo/denr_logo.png" alt="DENR Logo" class="h-12 w-12">
                    <div>
                        <span class="text-2xl font-black text-emerald-900 tracking-tighter block leading-none">O-LDPMS</span>
                        <span class="text-[10px] uppercase font-bold text-emerald-700 tracking-widest">DENR CARAGA REGION</span>
                    </div>
                </div>
                <div class="hidden lg:flex items-center space-x-8 font-semibold text-gray-600">
                    <a href="#home" class="hover:text-emerald-700 transition">HOME</a>
                    <a href="#about" class="hover:text-emerald-700 transition">ABOUT</a>
                    <a href="#requirements" class="hover:text-emerald-700 transition">REQUIREMENTS</a>
                    <a href="#contact" class="hover:text-emerald-700 transition">CONTACT US</a>
                </div>
                <div class="flex items-center gap-4">
                    <button class="text-sm font-bold text-emerald-900 hover:bg-emerald-50 px-4 py-2 rounded-lg transition">LOGIN</button>
                    <button class="bg-emerald-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-emerald-800 shadow-md transition">REGISTER</button>
                </div>
            </div>
        </div>
    </nav>

    <header id="home" class="hero-pattern text-white py-24 px-6">
        <div class="max-w-5xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">
                Online Lumber Dealer Permitting & <span class="text-emerald-400">Monitoring System</span>
            </h1>
            <p class="text-lg md:text-xl text-emerald-50/90 mb-10 max-w-3xl mx-auto leading-relaxed">
                Streamlining the registration and monitoring of lumber dealers across the CARAGA region for sustainable forest management.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#requirements" class="bg-white text-emerald-900 px-8 py-4 rounded-xl font-bold hover:bg-emerald-50 transition shadow-lg">View Requirements</a>
                <a href="#about" class="bg-emerald-600/30 backdrop-blur-md border border-emerald-400/50 px-8 py-4 rounded-xl font-bold hover:bg-emerald-600/50 transition">Learn More</a>
            </div>
        </div>
    </header>

    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-sm font-bold text-emerald-600 tracking-widest uppercase mb-2">Our Objectives</h2>
                    <h3 class="text-4xl font-extrabold text-gray-900 mb-8 leading-tight">Standardizing the Future of Lumber Management</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border-l-4 border-emerald-600 shadow-sm">
                            <i class="fas fa-check-circle text-emerald-600 mt-1"></i>
                            <p class="text-gray-700 font-medium leading-relaxed">Standardize and streamline the process flow regionwide.</p>
                        </li>
                        <li class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border-l-4 border-emerald-600 shadow-sm">
                            <i class="fas fa-tree text-emerald-600 mt-1"></i>
                            <p class="text-gray-700 font-medium leading-relaxed">Strengthen the protection and conservation of naturally grown timber.</p>
                        </li>
                        <li class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border-l-4 border-emerald-600 shadow-sm">
                            <i class="fas fa-bolt text-emerald-600 mt-1"></i>
                            <p class="text-gray-700 font-medium leading-relaxed">Improve access to service delivery and efficient processing.</p>
                        </li>
                        <li class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border-l-4 border-emerald-600 shadow-sm">
                            <i class="fas fa-chart-line text-emerald-600 mt-1"></i>
                            <p class="text-gray-700 font-medium leading-relaxed">Update statistics of log/lumber supply contracts and wood volume analysis.</p>
                        </li>
                    </ul>
                </div>
                <div class="bg-emerald-900 p-10 rounded-3xl text-white shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <i class="fas fa-quote-left text-5xl text-emerald-500 mb-6 opacity-50"></i>
                        <h4 class="text-2xl font-bold mb-6">DENR MANDATE</h4>
                        <p class="text-emerald-100/90 italic leading-loose text-lg">
                            "The DENR shall be the primary government agency responsible for the conservation, management, development, and proper use of the country’s environment and natural resources... in order to ensure equitable sharing of the benefits derived therefrom for the welfare of the present and future generations of Filipinos."
                        </p>
                    </div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-emerald-800 rounded-full blur-3xl opacity-50"></div>
                </div>
            </div>
        </div>
    </section>

    <section id="requirements" class="py-20 bg-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">List of Requirements</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Please ensure all documents are scanned and uploaded in <span class="text-red-600 font-bold">PDF Format</span>. The application form must be notarized.</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-800 text-white uppercase text-xs tracking-wider">
                                <th class="px-8 py-5">Document Name</th>
                                <th class="px-8 py-5 text-center">New Application</th>
                                <th class="px-8 py-5 text-center">For Renewal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-800">1. Application form duly accomplished & sworn/notarized</div>
                                    <a href="#" class="text-blue-600 text-xs hover:underline"><i class="fas fa-download mr-1"></i>Download Template</a>
                                </td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-800">2. Lumber Supply Contract/Agreement from legitimate suppliers</div>
                                    <span class="text-red-500 text-[10px] italic">Not required for mini-sawmill permittees</span>
                                </td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-800">3. Mayor's Permit / Business Permit and Certificate</div>
                                </td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5"><div class="font-bold text-gray-800">4. Annual Business Plan</div></td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5"><div class="font-bold text-gray-800">5. Latest Income Tax Return</div></td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5"><div class="font-bold text-gray-800">6. Pictures of Establishment inspected/verified by CENRO</div></td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                                <td class="px-8 py-5 text-center text-gray-300">—</td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5"><div class="font-bold text-gray-800">7. Ending stocked inventory report duly subscribed/sworn</div></td>
                                <td class="px-8 py-5 text-center text-gray-300">—</td>
                                <td class="px-8 py-5 text-center"><i class="fas fa-check-circle text-emerald-500 text-xl"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12">
            <div>
                <h3 class="text-3xl font-bold mb-6">Contact Us</h3>
                <form class="space-y-4">
                    <input type="text" placeholder="Your Name *" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition">
                    <input type="email" placeholder="Your Email address *" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition">
                    <input type="text" placeholder="Subject" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition">
                    <textarea rows="4" placeholder="Message *" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition"></textarea>
                    <button class="w-full md:w-auto bg-blue-600 text-white font-bold px-10 py-4 rounded-xl hover:bg-blue-700 shadow-lg transition">Send Message</button>
                </form>
            </div>
            <div class="flex flex-col justify-center">
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100">
                    <h4 class="text-2xl font-bold mb-6 text-emerald-900">DENR CARAGA Region</h4>
                    <div class="space-y-6">
                        <div class="flex gap-4 items-start">
                            <div class="bg-emerald-100 p-3 rounded-lg text-emerald-700"><i class="fas fa-map-marker-alt"></i></div>
                            <p class="text-gray-600 leading-relaxed">Ambago, Butuan City, Philippines, 8600</p>
                        </div>
                        <div class="flex gap-4 items-start">
                            <div class="bg-emerald-100 p-3 rounded-lg text-emerald-700"><i class="fas fa-envelope"></i></div>
                            <p class="text-gray-600">fuscaraga@yahoo.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-emerald-950 text-emerald-50 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-center md:text-left">
                <p class="font-bold text-lg">Department of Environment and Natural Resources</p>
                <p class="text-emerald-400 font-semibold mb-2">RICTU CARAGA Region</p>
                <p class="text-xs text-emerald-500">© Copyright 2022 - 2026. All Rights Reserved.</p>
            </div>
            <div class="flex gap-6 text-2xl">
                <a href="#" class="hover:text-white"><i class="fab fa-facebook"></i></a>
                <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-white"><i class="fas fa-globe"></i></a>
            </div>
        </div>
    </footer>

</body>
</html>


