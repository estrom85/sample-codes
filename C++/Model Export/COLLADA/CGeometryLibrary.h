#ifndef _CGEOMETRYLIBRARY_H_
#define _CGEOMETRYLIBRARY_H_
#include <vector>
#include <map>
#include <string>

#include "CSource.h"
#include "../pugixml.hpp"

using std::vector;
using std::map;
using std::string;

class CVcount
{
    public:
    unsigned int                    mCount;
    unsigned int                    mTotal;
    unsigned short                  *mNumbers;

    public:
        CVcount(){mNumbers=0; mCount=0;mTotal=0;};
        CVcount(const CVcount& vcount);
        ~CVcount();

        const CVcount& operator=(const CVcount& vcount);

    public:
        void load(pugi::xml_node &vcount, unsigned int count);
        unsigned int getTotal();

};

class CP
{
    public:
    unsigned int                    mCount;
    unsigned short                  *mIndices;

    public:
        CP(){mIndices=0; mCount=0;};
        CP(const CP& p);
        const CP& operator=(const CP& p);
        ~CP();

    public:
        void load(pugi::xml_node &p, unsigned int count);


};

class CInput
{
    public:
    string                          mSemantic;
    string                          mSource;
    string                          mOffset;
    string                          mSet;

    public:
        void load(pugi::xml_node &input);

};

class CFace
{
    public:
        unsigned int                    mCount;
        string                          mMaterial;
        vector<CInput>                  mInputs;
        CP                              mP;

    public:
        virtual ~CFace(){};
        virtual void load(pugi::xml_node &node)=0;

};

class CPolylist:public CFace
{
    public:

    CVcount                         mVcount;

    public:
        void load(pugi::xml_node &polylist);

};



class CTriangles:public CFace
{

    public:
        void load(pugi::xml_node &triangles);


};

class CVertices
{
    public:
    string                          mID;
    vector<CInput>                  mInputs;

    public:
        void load(pugi::xml_node &vertices);

};



class CMesh
{
    public:
        vector <CSource>            mSources;
        CVertices                   mVertices;
        vector<CTriangles>          mTriangles;
        vector<CPolylist>           mPolylist;

    public:
        void load(pugi::xml_node &mesh);



};


class CGeometry
{
    public:
        string                      mID;
        CMesh                       mMesh;


        void load(pugi::xml_node &geometry);

};


class CGeometryLibrary
{
    public:
        int                         currentGeometry;
        vector<CGeometry>           mGeometries;


        CGeometryLibrary(){currentGeometry=0;};
        void load(pugi::xml_node &library);
};




#endif
