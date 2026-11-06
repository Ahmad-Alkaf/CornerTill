import PrimaryButton from "@/Components/Buttons/PrimaryButton";
import { useTranslation } from "react-i18next";
import { FaGithub } from "react-icons/fa";
import { FaCodeBranch } from "react-icons/fa6";

export default function OpenSourceSection() {

  const { t } = useTranslation();
  return (
    <section id="public-source" className="overflow-hidden bg-[#475569] py-24">

      <div className="container mx-auto pb-24">
        <div className="flex flex-wrap justify-center">
          <div className="ms-auto me-auto w-full px-12 lg:mt-28 lg:w-5/12 lg:px-4">
            <div className="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-white p-3 text-center text-blue-gray-700 shadow-lg">
              <FaCodeBranch className="text-xl"/>
            </div>
            <h3 className="mb-2 text-3xl font-semibold leading-normal text-white">
              { t( "Public Source" ) }
            </h3>
            <p className="mb-4 mt-4 text-lg font-light leading-relaxed text-blue-gray-300">
              { t( "We value transparency and openness, and that is why our system is Public Source. You can access its code below and see how it's working.  You can also report issues, request features, or contribute to our system." ) }
            </p>
            <p className="mb-4 mt-0 text-lg font-light leading-relaxed text-blue-gray-300">
              { t( "The source code is free to use for noncommercial purposes under the terms of" ) }
              {" "}
              <a
                target="_blank"
                href="https://polyformproject.org/licenses/noncommercial/1.0.0/"
                className="text-blue-gray-400 hover:underline"
              >
                PolyForm Noncommercial 1.0.0.
              </a>
              {" "}
              { t( "If you like our system, please give us a star on GitHub." ) }
            </p>
            <a target="_blank" href="https://github.com/Ahmad-Alkaf/Laptop-POS">
              <PrimaryButton className="github-star mt-4 inline-block rounded !bg-blue-gray-900 px-6 py-4 text-sm font-bold shadow hover:!bg-black hover:shadow-lg">
                { t( "Source Code" ) }
              </PrimaryButton>
            </a>
          </div>
          <div className="relative ms-auto me-auto mt-32 w-full px-4 lg:w-4/12">
            <FaGithub
              className="fab fa-github end-[-50%] md:end-[-80%] md:text-[47rem] text-[27rem] text-black"
              style={{
                position: "absolute",
                top: "-170px",
                left: "auto",
                opacity: ".8",
              }}
            />
          </div>
        </div>
      </div>
    </section>
  );
}
