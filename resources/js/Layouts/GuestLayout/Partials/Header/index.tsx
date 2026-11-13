import { useEffect, useState } from "react";
import MobileMenu from "./Partials/MobileMenu";
import DesktopMenu from "./Partials/DesktopMenu";
import FullLogo from "@/Components/Logo/FullLogo";
import LanguageSwitch from "@/Components/LanguageSwitch";

export default function Header() {
  const [top, setTop] = useState<boolean>(true);

  // detect whether user has scrolled the page down by 10px
  const scrollHandler = () => {
    window.scrollY > 10 ? setTop(false) : setTop(true);
  };

  useEffect(() => {
    scrollHandler();
    window.addEventListener("scroll", scrollHandler);
    return () => window.removeEventListener("scroll", scrollHandler);
  }, [top]);

  return (
    <>
      <header
        style={{ zIndex: 100 }}
        className={`fixed w-full backdrop-blur-sm transition duration-300 ease-in-out md:bg-opacity-80 ${
          !top ? "bg-gray-50 shadow-lg" : ""
        }`}
      >
        <div className="mx-auto max-w-6xl px-5 sm:px-6">
          <div className="flex h-16 items-center justify-between md:h-20">
            {/* Site branding */}
            <FullLogo />
            <DesktopMenu />
            <div className="ms-4 flex items-center gap-3 md:ms-5">
              <LanguageSwitch />
              <MobileMenu />
            </div>
          </div>
        </div>
      </header>
      <div className="pb-16 md:pb-20"></div>
    </>
  );
}
