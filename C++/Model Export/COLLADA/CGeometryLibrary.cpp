#include "CGeometryLibrary.h"
#include <iostream.>
#include <sstream>

/***********************************Vcount***********************************************/
CVcount::CVcount(const CVcount& other)
{
    mCount=other.mCount;
    mNumbers=new unsigned short[mCount];

    for (int i=0;i<mCount;i++)
        mNumbers[i]=other.mNumbers[i];
}

const CVcount& CVcount::operator=(const CVcount& other)
{
    if(this==&other)
        return *this;

    mCount=other.mCount;
    delete[] mNumbers;
    mNumbers=new unsigned short[mCount];

    for (int i=0;i<mCount;i++)
        mNumbers[i]=other.mNumbers[i];

    return *this;
}

CVcount::~CVcount()
{
    delete[] mNumbers;
}

void CVcount::load(pugi::xml_node &vcount, unsigned int count)
{
    mCount=count;
    mTotal=0;
   // delete[] mNumbers;
    mNumbers = new unsigned short [mCount];
    //std::cout<<mCount<<std::endl;
    std::stringstream ss;
    ss<<vcount.child_value("vcount");

    int i=0;
    unsigned short temp;

    while(ss>>temp)
    {
        mNumbers[i]=temp;
        mTotal+=temp;
        i++;
    }
}

unsigned int CVcount::getTotal()
{
    return mTotal;
}

/*************************************P**************************************************/
CP::CP(const CP& other_p)
{
    mCount=other_p.mCount;
    mIndices=new unsigned short[mCount];

    for (int i=0; i<mCount;i++)
        mIndices[i]=other_p.mIndices[i];


}

const CP& CP::operator=(const CP& other_p)
{
    if (this==&other_p)
        return *this;


    mCount=other_p.mCount;
    delete [] mIndices;

    mIndices=new unsigned short[mCount];

    for (int i=0; i<mCount;i++)
        mIndices[i]=other_p.mIndices[i];

    return *this;

}

CP::~CP()
{
    delete[] mIndices;
}

void CP::load(pugi::xml_node &p,unsigned int count)
{
    mCount=count;

    delete[] mIndices;
    mIndices = new unsigned short[mCount];

    std::stringstream ss;
    ss.clear();
    ss<<p.child_value("p");


    unsigned short temp;
    int i=0;

    while(ss>>temp)
    {
        mIndices[i]=temp;
        i++;
    }
}

/***********************************Input************************************************/
void CInput::load(pugi::xml_node &input)
{
    mSemantic=input.attribute("semantic").value();
    mSource=input.attribute("source").value();
    mOffset=input.attribute("offset").value();
    mSet=input.attribute("set").value();
}

/**********************************Polylist**********************************************/
void CPolylist::load(pugi::xml_node &polylist)
{
    mCount=polylist.attribute("count").as_uint();
    mMaterial=polylist.attribute("material").value();


    for (pugi::xml_node input_node=polylist.child("input");input_node;input_node=input_node.next_sibling("input"))
    {
        CInput input;
        input.load(input_node);
        mInputs.push_back(input);

    }

    mVcount.load(polylist,mCount);
    mP.load(polylist,mInputs.size()*mVcount.getTotal());
}

/**********************************Triangles*********************************************/
void CTriangles::load(pugi::xml_node &triangles)
{
    mCount=triangles.attribute("count").as_uint();
    mMaterial=triangles.attribute("material").value();
    for (pugi::xml_node input_node=triangles.child("input");input_node;input_node=input_node.next_sibling("input"))
    {
        CInput input;
        input.load(input_node);
        mInputs.push_back(input);
    }
    mP.load(triangles,mCount*3*mInputs.size());
}

/*********************************Vertices***********************************************/
void CVertices::load(pugi::xml_node &vertices)
{
    mID=vertices.attribute("id").value();

    for (pugi::xml_node input_node=vertices.child("input");input_node;input_node=input_node.next_sibling("input"))
    {
        CInput input;
        input.load(input_node);
        mInputs.push_back(input);

    }


}


/************************************Mesh************************************************/

void CMesh::load(pugi::xml_node &mesh)
{
    for (pugi::xml_node src_node=mesh.child("source");src_node;src_node=src_node.next_sibling("source"))
    {
        CSource source;
        source.load(src_node);
        mSources.push_back(source);
    }

    pugi::xml_node vert_node=mesh.child("vertices");
    mVertices.load(vert_node);

    for(pugi::xml_node tri_node=mesh.child("triangles");tri_node;tri_node=tri_node.next_sibling("triangles"))
    {
        CTriangles triangle;
        triangle.load(tri_node);
        mTriangles.push_back(triangle);
    }

    for(pugi::xml_node poly_node=mesh.child("polylist");poly_node;poly_node=poly_node.next_sibling("polylist"))
    {

        CPolylist polylist;

        polylist.load(poly_node);

        mPolylist.push_back(polylist);

    }

}

/***********************************Geometry*********************************************/

void CGeometry::load(pugi::xml_node &geometry)
{
    pugi::xml_node mesh=geometry.child("mesh");
    mMesh.load(mesh);
    mID=geometry.attribute("id").value();

}

/*******************************Geometry Library*****************************************/
void CGeometryLibrary::load(pugi::xml_node &library)
{
    for (pugi::xml_node geom_node=library.child("geometry");geom_node;geom_node=geom_node.next_sibling("geometry"))
    {
        CGeometry geometry;
        geometry.load(geom_node);
        mGeometries.push_back(geometry);

    }


}




