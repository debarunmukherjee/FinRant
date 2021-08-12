import React from 'react';
import LandingHeader from "@/Pages/HomepageComponents/LadingHeader";
import HeroSection from "@/Pages/HomepageComponents/HeroSection";
import Features from "@/Pages/HomepageComponents/Features";
import FeatureBlocks from "@/Pages/HomepageComponents/FeatureBlocks";
import Footer from "@/Layouts/Footer";

export default function Welcome() {
    return (
        <div className="flex flex-col min-h-screen overflow-hidden">
            <LandingHeader/>
            <main className="flex-grow">
                <HeroSection/>
                <Features/>
                <FeatureBlocks/>
            </main>
            <Footer/>
        </div>
    );
}
