import React, { useEffect } from 'react';
import { Head, Link } from '@inertiajs/react';
import Header from '@/Components/Header';
import Footer from '@/Components/Footer';
import { Calendar, Bell, Tag, ArrowLeft, Clock, ExternalLink } from 'lucide-react';

export default function Announcement({ announcement }) {
    // Scroll to top when component mounts
    useEffect(() => {
        window.scrollTo(0, 0);
    }, []);

    return (
        <>
            <Head title={`${announcement.title} - Speed Up Tutorial Center`} />
            
            <Header activeSection="announcements" />

            {/* Main Content */}
            <main className="w-full bg-orange-50 min-h-screen py-12 px-4">
                <div className="max-w-4xl mx-auto">
                    {/* Back Button */}
                    <div className="mb-8">
                        <Link 
                            href={route('announcements')}
                            className="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-lg transition-colors shadow-sm"
                        >
                            <ArrowLeft className="h-4 w-4 mr-2" />
                            Back to Announcements
                        </Link>
                    </div>

                    {/* Announcement Card */}
                    <div className="bg-white rounded-2xl overflow-hidden shadow-xl border-b-4 border-orange-500">
                        {/* Header with Badge and Date */}
                        <div className="p-6 border-b border-gray-100">
                            <div className="flex flex-wrap items-center justify-between gap-4 mb-4">
                                <div className="flex items-center space-x-4">
                                    <div className="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-full text-sm font-medium shadow-lg">
                                        <Bell className="h-4 w-4 mr-2" />
                                        <span>{announcement.badge || 'Announcement'}</span>
                                    </div>
                                    {announcement.tags && announcement.tags.length > 0 && (
                                        <div className="flex flex-wrap gap-2">
                                            {announcement.tags.map((tag, index) => (
                                                <span 
                                                    key={index}
                                                    className="inline-flex items-center px-2 py-1 bg-navy-100 text-navy-800 rounded-full text-xs font-medium"
                                                >
                                                    <Tag className="h-3 w-3 mr-1" />
                                                    #{tag.replace('-', '')}
                                                </span>
                                            ))}
                                        </div>
                                    )}
                                </div>
                                <div className="flex items-center text-gray-600">
                                    <Calendar className="h-5 w-5 mr-2" />
                                    <span className="text-sm font-medium">{announcement.date}</span>
                                </div>
                            </div>

                            {/* Expiry Notice */}
                            {announcement.expires_at && (
                                <div className="flex items-center p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg">
                                    <Clock className="h-5 w-5 text-yellow-600 mr-3" />
                                    <div>
                                        <p className="text-sm font-medium text-yellow-800">Limited Time Offer</p>
                                        <p className="text-xs text-yellow-700">Expires on {announcement.expires_at}</p>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Hero Image */}
                        {announcement.image && (
                            <div className="relative h-96 overflow-hidden">
                                <img 
                                    src={announcement.image} 
                                    alt={announcement.title}
                                    className="w-full h-full object-cover"
                                    onError={(e) => {
                                        e.target.onerror = null;
                                        e.target.src = 'https://placehold.co/800x400?text=No+Image';
                                    }}
                                />
                                <div className="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                            </div>
                        )}

                        {/* Content */}
                        <div className="p-8">
                            <h1 className="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                                {announcement.title}
                            </h1>

                            {/* Content Body */}
                            <div className="prose prose-lg prose-orange max-w-none">
                                {announcement.content && announcement.content.length > 0 ? (
                                    announcement.content.map((paragraph, index) => (
                                        <p key={index} className="text-gray-700 leading-relaxed mb-4">
                                            {paragraph}
                                        </p>
                                    ))
                                ) : (
                                    <p className="text-gray-700 leading-relaxed">
                                        Stay tuned for more details about this announcement.
                                    </p>
                                )}
                            </div>

                            {/* Action Buttons */}
                            <div className="mt-10 pt-6 border-t border-gray-100">
                                <div className="flex flex-col sm:flex-row gap-4">
                                    <a 
                                        href={`mailto:contact@speeduptutorial.com?subject=Inquiry about: ${announcement.title}`}
                                        className="inline-flex items-center justify-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors shadow-lg"
                                    >
                                        Contact Us About This
                                        <ExternalLink className="ml-2 h-4 w-4" />
                                    </a>
                                    <Link 
                                        href={route('inquiry')}
                                        className="inline-flex items-center justify-center px-6 py-3 bg-navy-600 hover:bg-navy-700 text-white font-medium rounded-lg transition-colors shadow-lg"
                                    >
                                        Make an Inquiry
                                    </Link>
                                    <Link 
                                        href={route('announcements')}
                                        className="inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors shadow-lg"
                                    >
                                        View All Announcements
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Related Information */}
                    <div className="mt-8 bg-white rounded-xl p-6 shadow-lg">
                        <h3 className="text-xl font-semibold text-gray-900 mb-4">Need More Information?</h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="flex items-start space-x-4">
                                <div className="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                    <Bell className="h-5 w-5 text-orange-600" />
                                </div>
                                <div>
                                    <h4 className="font-medium text-gray-900">Stay Updated</h4>
                                    <p className="text-sm text-gray-600 mt-1">
                                        Follow our announcements page for the latest updates and offers.
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-start space-x-4">
                                <div className="flex-shrink-0 w-10 h-10 bg-navy-100 rounded-full flex items-center justify-center">
                                    <ExternalLink className="h-5 w-5 text-navy-600" />
                                </div>
                                <div>
                                    <h4 className="font-medium text-gray-900">Contact Support</h4>
                                    <p className="text-sm text-gray-600 mt-1">
                                        Have questions? Reach out to us for personalized assistance.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <Footer />
        </>
    );
}