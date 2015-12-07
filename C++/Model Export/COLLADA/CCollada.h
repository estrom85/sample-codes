#ifndef _CCOLLADA_H_
#define _CCOLLADA_H_

#include "CGeometryLibrary.h"
#include "CImageLibrary.h"
#include "CMaterialLibrary.h"
#include "CEffectLibrary.h"


#include "../pugixml.hpp"
#include <iostream>
#include <windows.h>
#include <gl/gl.h>
#include <gl/glu.h>


enum
{
    QUAD_MODE=0,
    POLYGON_MODE
};

enum
{
    POSITION=0,
    NORMAL,
    TEXCOORD
};

class CCollada
{
    public:
        CGeometryLibrary            mGeometryLibrary;
        CImageLibrary               mImageLibrary;
        CMaterialLibrary            mMaterialLibrary;
        CEffectLibrary              mEffectLibrary;


    public:
       CSource* getVertexSource(CMesh *mesh);
       CSource* getSource(CMesh* mesh,string ID);
       void setOffset(CSource* src, int &x, int &y, int &z);
       float getFloatCoord(CSource *src, unsigned int pos, int offset);

    public:
        CCollada(const char* filename);

    public:
        int getNumberOfGeometries();
        void setCurrentGeometry(int geometryIndex);
        char* getTextureFile();



    public:
        void drawGeometry(int geometryIndex);

        const char* getGeometryID(unsigned int index);

};


#endif

