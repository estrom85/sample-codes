#ifndef _CMATERIALLIBRARY_H_
#define _CMATERIALLIBRARY_H_

#include <vector>
#include <string>
#include "../pugixml.hpp"

class CInstanceEffect
{
    public:
        std::string         mURL;
};

class CMaterial
{
    public:
        std::string         mID;
        CInstanceEffect     mEfect;
};

class CMaterialLibrary
{
    public:
        std::vector<CMaterial>  mMaterials;

    public:
        void load(pugi::xml_node &node);


};


#endif
