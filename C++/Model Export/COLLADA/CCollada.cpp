#include "CCollada.h"
#include <sstream>

CCollada::CCollada(const char *filename)
{
    pugi::xml_document collada;
    pugi::xml_parse_result result=collada.load_file(filename);

    if(result.status==pugi::status_ok)
    {
        pugi::xml_node geometry_library=collada.child("COLLADA").child("library_geometries");
        mGeometryLibrary.load(geometry_library);

        pugi::xml_node image_library=collada.child("COLLADA").child("library_images");
        mImageLibrary.load(image_library);

        pugi::xml_node material_library=collada.child("COLLADA").child("library_materials");
        mMaterialLibrary.load(material_library);

        pugi::xml_node effect_library=collada.child("COLLADA").child("library_effects");
        mEffectLibrary.load(effect_library);

    }
}

int CCollada::getNumberOfGeometries()
{
    return mGeometryLibrary.mGeometries.size();
}

void CCollada::setCurrentGeometry(int geometryIndex)
{
    mGeometryLibrary.currentGeometry=geometryIndex;
}




CSource* CCollada::getVertexSource(CMesh *mesh)
{
    int numOfInputs=mesh->mVertices.mInputs.size();
    string sourceID;
    for(int i=0;i<numOfInputs;i++)
    {
        if(mesh->mVertices.mInputs[i].mSemantic=="POSITION")
            sourceID=mesh->mVertices.mInputs[i].mSource;
    }

    return getSource(mesh,sourceID);
}
CSource* CCollada::getSource(CMesh* mesh, string ID)
{
    int numberOfSources=mesh->mSources.size();
    CSource* source=0;

    for (int i=0;i<numberOfSources;i++)
    {
        source=&mesh->mSources[i];
        if(ID==string("#")+source->mID)
            break;
    }

    return source;
}

void CCollada::setOffset(CSource *source, int &x, int &y, int &z)
{
    int numberOfParams=source->mTechniqueCommon.mAccessor.mParams.size();

    for(int i=0;i<numberOfParams;i++)
    {
        string name=source->mTechniqueCommon.mAccessor.mParams[i].mName;
        if(name=="X"||name=="S") x=i;
        if(name=="Y"||name=="T") y=i;
        if(name=="Z"||name=="P") z=i;
    }
}

float CCollada::getFloatCoord(CSource* src, unsigned int pos, int offset)
{
    int stride=src->mTechniqueCommon.mAccessor.mStride;
    if (src->mFloatArray.mCount<=pos*stride) return 0.0f;


    return src->mFloatArray.mFloats[stride*pos+offset];
}

void CCollada::drawGeometry(int geometryIndex)
{
    std::stringstream ss;

    if(geometryIndex<0||geometryIndex>mGeometryLibrary.mGeometries.size())
        geometryIndex=0;

    CMesh *mesh=&mGeometryLibrary.mGeometries[geometryIndex].mMesh;
    unsigned int numOfTrianglesArrays=mesh->mTriangles.size();
    unsigned int numOfPolylist=mesh->mPolylist.size();
    CTriangles *triangles=0;
    CPolylist *polylist=0;

    CSource *vertexSource=getVertexSource(mesh);
    CSource *normalsSource=0;
    CSource *texSource=0;

/*****************triangles*************************/

    for (int i=0;i<numOfTrianglesArrays;i++)
    {
        triangles=&mesh->mTriangles[i];
        int stride=triangles->mInputs.size();
        string normSrcID;
        int vertOffset;
        int normOffset;

        for(int i=0;i<stride;i++)
        {
            if(triangles->mInputs[i].mSemantic=="VERTEX")
            {
                ss<<triangles->mInputs[i].mOffset;
                ss>>vertOffset;
                ss.clear();
            }
            if(triangles->mInputs[i].mSemantic=="NORMAL")
            {
                normSrcID=triangles->mInputs[i].mSource;
                ss<<triangles->mInputs[i].mOffset;
                ss>>normOffset;
            }


        }
        normalsSource=getSource(mesh,normSrcID);


        int vertXOffset;
        int vertYOffset;
        int vertZOffset;
        int normXOffset;
        int normYOffset;
        int normZOffset;

        setOffset(vertexSource,vertXOffset,vertYOffset,vertZOffset);
        setOffset(normalsSource,normXOffset,normYOffset,normZOffset);

       // std::cout<<getFloatCoord(vertexSource,2618,vertYOffset);
        glBegin(GL_TRIANGLES);

        for(int i=0;i<3*triangles->mCount;i++)
        {
            unsigned int vertIndex=triangles->mP.mIndices[i*stride+vertOffset];
            unsigned int normIndex=triangles->mP.mIndices[i*stride+normOffset];


            float vertX=getFloatCoord(vertexSource,vertIndex,vertXOffset);
            float vertY=getFloatCoord(vertexSource,vertIndex,vertYOffset);
            float vertZ=getFloatCoord(vertexSource,vertIndex,vertZOffset);


            //std::cout<<getFloatCoord(normalsSource,normIndex,normXOffset);
            float normX=getFloatCoord(normalsSource,normIndex,normXOffset);

            float normY=getFloatCoord(normalsSource,normIndex,normYOffset);
            float normZ=getFloatCoord(normalsSource,normIndex,normZOffset);



            glNormal3f(normX, normY, normZ);
            glVertex3f(vertX, vertY, vertZ);
        }

        glEnd();

    }

    /**********************polylists******************************/

    for(int i=0;i<numOfPolylist;i++)
    {
        polylist=&mesh->mPolylist[i];
        int stride=polylist->mInputs.size();
        string normSrcID;
        string texSrcID;
        int vertOffset;
        int normOffset;
        int texOffset;


        for(int i=0;i<stride;i++)
        {
            if(polylist->mInputs[i].mSemantic=="VERTEX")
            {
                ss<<polylist->mInputs[i].mOffset;
                ss>>vertOffset;
                ss.clear();
            }
            if(polylist->mInputs[i].mSemantic=="NORMAL")
            {
                normSrcID=polylist->mInputs[i].mSource;
                ss<<polylist->mInputs[i].mOffset;
                ss>>normOffset;
                ss.clear();
            }
            if(polylist->mInputs[i].mSemantic=="TEXCOORD")
            {
                texSrcID=polylist->mInputs[i].mSource;
                ss<<polylist->mInputs[i].mOffset;
                ss>>texOffset;
                ss.clear();

            }

        }
        normalsSource=getSource(mesh,normSrcID);
        texSource=getSource(mesh,texSrcID);

        int vertXOffset;
        int vertYOffset;
        int vertZOffset;
        int normXOffset;
        int normYOffset;
        int normZOffset;
        int texSOffset;
        int texTOffset;
        int texPOffset;

        setOffset(vertexSource,vertXOffset,vertYOffset,vertZOffset);
        setOffset(normalsSource,normXOffset,normYOffset,normZOffset);
        setOffset(texSource,texSOffset,texTOffset,texPOffset);

        int count=0;
        glBegin(GL_QUADS);
        for(int i=0;i<4*polylist->mCount;i++)
        {

            count+=1;
            unsigned int vertIndex=polylist->mP.mIndices[i*stride+vertOffset];
            unsigned int normIndex=polylist->mP.mIndices[i*stride+normOffset];
            unsigned int texIndex=polylist->mP.mIndices[i*stride+texOffset];


            float vertX=getFloatCoord(vertexSource,vertIndex,vertXOffset);
            float vertY=getFloatCoord(vertexSource,vertIndex,vertYOffset);
            float vertZ=getFloatCoord(vertexSource,vertIndex,vertZOffset);


            //std::cout<<getFloatCoord(normalsSource,normIndex,normXOffset);
            float normX=getFloatCoord(normalsSource,normIndex,normXOffset);

            float normY=getFloatCoord(normalsSource,normIndex,normYOffset);
            float normZ=getFloatCoord(normalsSource,normIndex,normZOffset);

            float texS=getFloatCoord(texSource,texIndex,texSOffset);
            float texT=getFloatCoord(texSource,texIndex,texTOffset);



            glNormal3f(normX, normY, normZ);
            glTexCoord2f(texS,texT);
            glVertex3f(vertX, vertY, vertZ);
        }

        //std::cout<<count<<" ";
        glEnd();

    }

}

const char* CCollada::getGeometryID(unsigned int index)
{

    return mGeometryLibrary.mGeometries[index].mID.c_str();
}


