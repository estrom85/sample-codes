#ifndef _CIMAGELIBRARY_H_
#define _CIMAGELIBRARY_H_

#include "../pugixml.hpp"

#include <vector>
#include <string>
using std::vector;
using std::string;

class CInitFrom
{
    public:
        string mFilename;
};

class CImageFile
{
    public:
        string mID;
        CInitFrom mInitFrom;
};

class CImageLibrary
{
    public:
        vector<CImageFile> mImages;

    public:
        void load(pugi::xml_node& node);

};

#endif
